<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Models\WhatsappBusinessAccount;
use App\Models\WhatsappMessageLog;
use App\Models\WhatsappConversation;
use App\Services\CalendarService;
use App\Services\WhatsAppService;
use App\Jobs\ReleaseExpiredSlotHold;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProcessIncomingWhatsAppMessage implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $waba;
    protected $message;
    protected $value;

    public function __construct(WhatsappBusinessAccount $waba, array $message, array $value)
    {
        $this->waba = $waba;
        $this->message = $message;
        $this->value = $value;
    }

    public function handle(CalendarService $calendarService, WhatsAppService $whatsAppService): void
    {
        $messageId = $this->message['id'];
        $from = $this->message['from'];
        
        // 1. Parsear el mensaje entrante (soportando text, list_reply y button_reply)
        $messageType = $this->message['type'] ?? 'unknown';
        $incomingText = '';
        $selectedOptionId = null;

        if ($messageType === 'text') {
            $incomingText = trim($this->message['text']['body'] ?? '');
        } elseif ($messageType === 'interactive') {
            $interactiveType = $this->message['interactive']['type'] ?? null;
            if ($interactiveType === 'list_reply') {
                $selectedOptionId = $this->message['interactive']['list_reply']['id'] ?? null;
                $incomingText = $this->message['interactive']['list_reply']['title'] ?? '';
            } elseif ($interactiveType === 'button_reply') {
                $selectedOptionId = $this->message['interactive']['button_reply']['id'] ?? null;
                $incomingText = $this->message['interactive']['button_reply']['title'] ?? '';
            }
        }

        // 2. Procesar únicamente los cambios locales de la base de datos dentro de la transacción
        $conversation = DB::transaction(function () use ($messageId, $from) {
            
            $conv = WhatsappConversation::firstOrCreate(
                [
                    'company_id' => $this->waba->company_id,
                    'patient_phone' => $from
                ],
                [
                    'state' => 'inicio',
                    'last_message_at' => now(),
                ]
            );

            // Registrar log para idempotencia
            WhatsappMessageLog::create([
                'company_id' => $this->waba->company_id,
                'whatsapp_conversation_id' => $conv->id,
                'whatsapp_message_id' => $messageId,
                'direction' => 'inbound',
                'message_type' => $this->message['type'] ?? 'unknown',
                'payload' => $this->message
            ]);

            // Expiración si pasaron más de 2 horas (comparación timezone-safe)
            if ($conv->last_message_at->copy()->setTimezone('UTC')->diffInHours(now()->setTimezone('UTC')) >= 2) {
                $conv->state = 'inicio';
                $conv->context_json = null;
            }

            $conv->last_message_at = now();
            $conv->save();

            return $conv;
        });

        // 3. Ejecutar la máquina de estados y transiciones de WhatsApp (fuera de la transacción)
        $this->processStateTransition($conversation, $incomingText, $selectedOptionId, $calendarService, $whatsAppService);
    }

    /**
     * Controlador central de la máquina de estados.
     */
    protected function processStateTransition(
        WhatsappConversation $conversation,
        string $incomingText,
        ?string $selectedOptionId,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        $state = $conversation->state;

        switch ($state) {
            case 'inicio':
                $this->handleStateInicio($conversation, $whatsAppService);
                break;

            case 'esperando_seleccion_menu':
                $this->handleStateEsperandoMenu($conversation, $incomingText, $selectedOptionId, $calendarService, $whatsAppService);
                break;

            case 'esperando_seleccion_profesional':
                $this->handleStateEsperandoProfesional($conversation, $selectedOptionId, $calendarService, $whatsAppService);
                break;

            case 'esperando_seleccion_tipo_turno':
                $this->handleStateEsperandoAppointmentType($conversation, $selectedOptionId, $calendarService, $whatsAppService);
                break;

            case 'esperando_seleccion_dia':
                $this->handleStateEsperandoDia($conversation, $selectedOptionId, $calendarService, $whatsAppService);
                break;

            case 'esperando_seleccion_horario':
                $this->handleStateEsperandoHorario($conversation, $selectedOptionId, $calendarService, $whatsAppService);
                break;

            case 'esperando_confirmacion_identidad':
                $this->handleStateConfirmacionIdentidad($conversation, $selectedOptionId, $calendarService, $whatsAppService);
                break;

            case 'esperando_tipo_correccion':
                $this->handleStateTipoCorreccion($conversation, $selectedOptionId, $calendarService, $whatsAppService);
                break;

            case 'esperando_nombre':
                $this->handleStateEsperandoNombre($conversation, $incomingText, $calendarService, $whatsAppService);
                break;

            case 'esperando_apellido':
                $this->handleStateEsperandoApellido($conversation, $incomingText, $calendarService, $whatsAppService);
                break;

            case 'esperando_obra_social':
                $this->handleStateEsperandoObraSocial($conversation, $incomingText, $calendarService, $whatsAppService);
                break;

            case 'esperando_email':
                $this->handleStateEsperandoEmail($conversation, $incomingText, $selectedOptionId, $calendarService, $whatsAppService);
                break;

            case 'esperando_dni':
                $this->handleStateEsperandoDni($conversation, $incomingText, $calendarService, $whatsAppService);
                break;

            case 'cancelacion_esperando_dni':
                $this->handleStateCancelacionEsperandoDni($conversation, $incomingText, $whatsAppService);
                break;

            case 'cancelacion_seleccion_turno':
                $this->handleStateCancelacionSeleccionTurno($conversation, $selectedOptionId, $whatsAppService);
                break;

            case 'cancelacion_confirmacion':
                $this->handleStateCancelacionConfirmacion($conversation, $selectedOptionId, $calendarService, $whatsAppService);
                break;

            case 'finalizado':
                // Si la sesión ya finalizó, reiniciamos el flujo
                $conversation->update([
                    'state' => 'inicio',
                    'context_json' => null
                ]);
                $this->handleStateInicio($conversation, $whatsAppService);
                break;

            default:
                Log::channel('whatsapp')->error("Unknown state: {$state} for conversation: {$conversation->id}");
                break;
        }
    }

    /**
     * Estado: inicio (Muestra el Menú Principal)
     */
    protected function handleStateInicio(
        WhatsappConversation $conversation,
        WhatsAppService $whatsAppService
    ) {
        $conversation->update([
            'state' => 'esperando_seleccion_menu',
            'context_json' => null // Limpiar contexto anterior
        ]);

        $company = $this->waba->company ?? \App\Models\Company::find($this->waba->company_id);
        
        $activeProfessionalsCount = $company->professionals()->where('is_active', true)->count();
        if ($activeProfessionalsCount > 1) {
            $displayName = $company->name;
        } else {
            $displayName = $company->professional_name ?? $company->name;
        }

        $whatsAppService->sendReplyButtons(
            $this->waba,
            $conversation->patient_phone,
            "Hola, bienvenido al turnero de {$displayName}. ¿En qué podemos ayudarte hoy? Por favor, selecciona una opción:",
            [
                'menu_new_appointment' => 'Nuevo turno',
                'menu_cancel_appointment' => 'Cancelar turno',
                'menu_location' => 'Nuestra ubicación'
            ]
        );
    }

    /**
     * Estado: esperando_seleccion_menu
     */
    protected function handleStateEsperandoMenu(
        WhatsappConversation $conversation,
        string $incomingText,
        ?string $selectedOptionId,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        $company = $this->waba->company ?? \App\Models\Company::find($this->waba->company_id);

        if ($selectedOptionId === 'menu_new_appointment') {
            $professionals = $company->professionals()->where('is_active', true)->get();

            if ($professionals->isEmpty()) {
                $whatsAppService->sendTextMessage(
                    $this->waba,
                    $conversation->patient_phone,
                    "Lo sentimos, el turnero está deshabilitado temporariamente porque no hay profesionales disponibles en este momento. Por favor contacta al consultorio directamente."
                );
                $conversation->update([
                    'state' => 'inicio',
                    'context_json' => null
                ]);
                return;
            }

            if ($professionals->count() > 1) {
                $this->handleStatePromptProfesional($conversation, $professionals, $whatsAppService);
                return;
            }

            // Un solo profesional activo, preseleccionar
            $context = $conversation->context_json ?? [];
            $context['professional_id'] = $professionals->first()->id;
            $conversation->update([
                'context_json' => $context,
            ]);

            $this->handleTransitionToAppointmentType($conversation, $company, $whatsAppService, $calendarService);
            return;
        }

        if ($selectedOptionId === 'menu_cancel_appointment') {
            $conversation->update(['state' => 'cancelacion_esperando_dni']);
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "Entendido. Para cancelar un turno, por favor escribe tu DNI (solo números, sin puntos ni espacios):"
            );
            return;
        }

        if ($selectedOptionId === 'menu_location') {
            $parts = [];
            
            $header = '';
            if (!empty($company->professional_title) || !empty($company->professional_name)) {
                $header = "📍 " . trim(($company->professional_title ?? '') . ' ' . ($company->professional_name ?? ''));
            }
            if (!empty($company->specialty)) {
                $header = $header ? "{$header} - {$company->specialty}" : "📍 {$company->specialty}";
            }
            if ($header) {
                $parts[] = $header;
            }

            if (!empty($company->name)) {
                $parts[] = $company->name;
            }

            $addressParts = [];
            if (!empty($company->address)) $addressParts[] = $company->address;
            if (!empty($company->city)) $addressParts[] = $company->city;
            if (!empty($company->state)) $addressParts[] = $company->state;
            if (count($addressParts) > 0) {
                $parts[] = implode(', ', $addressParts);
            }

            if (!empty($company->phone)) {
                $parts[] = "📞 " . $company->phone;
            }

            $message = implode("\n", $parts);

            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                $message ?: "No hay información de ubicación registrada."
            );

            // Volver al estado inicio
            $conversation->update([
                'state' => 'inicio',
                'context_json' => null
            ]);
            $this->handleStateInicio($conversation, $whatsAppService);
            return;
        }

        // Texto libre en este estado: reenviar el menú
        $whatsAppService->sendReplyButtons(
            $this->waba,
            $conversation->patient_phone,
            "Por favor, elige una opción de las siguientes:",
            [
                'menu_new_appointment' => 'Nuevo turno',
                'menu_cancel_appointment' => 'Cancelar turno',
                'menu_location' => 'Nuestra ubicación'
            ]
        );
    }

    protected function handleStatePromptProfesional(
        WhatsappConversation $conversation,
        $professionals,
        WhatsAppService $whatsAppService
    ) {
        $conversation->update(['state' => 'esperando_seleccion_profesional']);

        $options = [];
        foreach ($professionals as $prof) {
            $options["prof_{$prof->id}"] = [
                'title' => $prof->name,
                'description' => $prof->specialty ?? 'General'
            ];
        }

        $whatsAppService->sendListMessage(
            $this->waba,
            $conversation->patient_phone,
            "Por favor, seleccioná el profesional con el que deseas agendar tu turno:",
            "Ver Profesionales",
            "Profesionales disponibles",
            $options
        );
    }

    protected function handleStateEsperandoProfesional(
        WhatsappConversation $conversation,
        ?string $selectedOptionId,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        if (!$selectedOptionId || !str_starts_with($selectedOptionId, 'prof_')) {
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "Por favor, selecciona uno de los profesionales de la lista para continuar."
            );
            return;
        }

        $profId = (int) substr($selectedOptionId, 5); // Remueve 'prof_'
        
        $context = $conversation->context_json ?? [];
        $context['professional_id'] = $profId;
        
        $conversation->update([
            'context_json' => $context,
        ]);

        $company = $this->waba->company ?? \App\Models\Company::find($this->waba->company_id);
        $this->handleTransitionToAppointmentType($conversation, $company, $whatsAppService, $calendarService);
    }

    protected function handleTransitionToAppointmentType(
        WhatsappConversation $conversation,
        Company $company,
        WhatsAppService $whatsAppService,
        CalendarService $calendarService
    ) {
        $appointmentTypes = $company->appointmentTypes()->where('is_active', true)->get();

        if ($appointmentTypes->isEmpty()) {
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "Lo sentimos, el turnero está deshabilitado temporariamente porque no hay servicios disponibles en este momento. Por favor contacta al consultorio directamente."
            );
            $conversation->update([
                'state' => 'inicio',
                'context_json' => null
            ]);
            return;
        }

        if ($appointmentTypes->count() > 1) {
            $this->handleStatePromptAppointmentType($conversation, $appointmentTypes, $whatsAppService);
            return;
        }

        // Un solo tipo de turno, preseleccionar
        $context = $conversation->context_json ?? [];
        $context['appointment_type_id'] = $appointmentTypes->first()->id;
        $conversation->update([
            'context_json' => $context,
        ]);

        $this->handleStateMostrarDisponibilidad($conversation, $calendarService, $whatsAppService);
    }

    protected function handleStatePromptAppointmentType(
        WhatsappConversation $conversation,
        $appointmentTypes,
        WhatsAppService $whatsAppService
    ) {
        $conversation->update(['state' => 'esperando_seleccion_tipo_turno']);

        $options = [];
        foreach ($appointmentTypes as $type) {
            $options["type_{$type->id}"] = [
                'title' => $type->name,
                'description' => $type->duration . " min" . ($type->price > 0 ? " | $" . number_format($type->price, 0) : "")
            ];
        }

        $whatsAppService->sendListMessage(
            $this->waba,
            $conversation->patient_phone,
            "Por favor, seleccioná el tipo de consulta que deseas agendar:",
            "Ver Consultas",
            "Tipos de consulta",
            $options
        );
    }

    protected function handleStateEsperandoAppointmentType(
        WhatsappConversation $conversation,
        ?string $selectedOptionId,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        if (!$selectedOptionId || !str_starts_with($selectedOptionId, 'type_')) {
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "Por favor, selecciona uno de los tipos de consulta de la lista para continuar."
            );
            return;
        }

        $typeId = (int) substr($selectedOptionId, 5); // Remueve 'type_'
        
        $context = $conversation->context_json ?? [];
        $context['appointment_type_id'] = $typeId;
        
        $conversation->update([
            'context_json' => $context,
            'state' => 'esperando_seleccion_dia'
        ]);

        $this->handleStateMostrarDisponibilidad($conversation, $calendarService, $whatsAppService);
    }

    /**
     * Muestra la disponibilidad de días para un nuevo turno (antiguo handleStateInicio)
     */
    protected function handleStateMostrarDisponibilidad(
        WhatsappConversation $conversation,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        $company = $this->waba->company ?? \App\Models\Company::find($this->waba->company_id);
        $professionalId = $conversation->context_json['professional_id'] ?? null;
        $appointmentTypeId = $conversation->context_json['appointment_type_id'] ?? null;
        $availableDays = $calendarService->getAvailableDaysInRange($company, now(), now()->addDays(30), $professionalId, $appointmentTypeId, 10);

        if (count($availableDays) === 0) {
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "Lo sentimos, no hay turnos disponibles para los próximos 30 días. Por favor, intenta de nuevo más tarde."
            );
            $conversation->update([
                'state' => 'inicio',
                'context_json' => null
            ]);
            return;
        }

        $result = $whatsAppService->sendListMessage(
            $this->waba,
            $conversation->patient_phone,
            "Hola, bienvenido al turnero de " . ($company->professional_name ?? $company->name) . ". Por favor, selecciona el día para tu turno de la siguiente lista:",
            "Ver días",
            "Días Disponibles",
            $availableDays
        );

        if ($result) {
            $conversation->update(['state' => 'esperando_seleccion_dia']);
        } else {
            Log::channel('whatsapp')->warning('whatsapp_send_failed', [
                'recipient' => $conversation->patient_phone,
                'state' => 'mostrar_disponibilidad'
            ]);
        }
    }

    /**
     * Estado: esperando_seleccion_dia
     */
    protected function handleStateEsperandoDia(
        WhatsappConversation $conversation,
        ?string $selectedOptionId,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        $company = $this->waba->company ?? \App\Models\Company::find($this->waba->company_id);

        if ($selectedOptionId && preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedOptionId)) {
            $professionalId = $conversation->context_json['professional_id'] ?? null;
            $appointmentTypeId = $conversation->context_json['appointment_type_id'] ?? null;
            $rawSlots = $calendarService->getAvailableSlots($company, $date, $professionalId, $appointmentTypeId);
            $slots = array_values($rawSlots); // Re-indexación secuencial del array

            if (count($slots) > 0) {
                $context = $conversation->context_json ?? [];
                $context['date'] = $selectedOptionId;
                
                $conversation->update([
                    'context_json' => $context,
                    'state' => 'esperando_seleccion_horario'
                ]);

                $limitedSlots = array_slice($slots, 0, 10);
                $options = array_combine($limitedSlots, $limitedSlots);

                $whatsAppService->sendListMessage(
                    $this->waba,
                    $conversation->patient_phone,
                    "Seleccionaste el día " . $date->translatedFormat('l d/m') . ". Ahora selecciona el horario para tu turno:",
                    "Ver horarios",
                    "Horarios Disponibles",
                    $options
                );
                return;
            }
        }

        $whatsAppService->sendTextMessage(
            $this->waba,
            $conversation->patient_phone,
            "Por favor, selecciona una de las opciones que te enviamos en el botón de la lista de días."
        );
        
        $this->handleStateMostrarDisponibilidad($conversation, $calendarService, $whatsAppService);
    }

    /**
     * Estado: esperando_seleccion_horario
     */
    protected function handleStateEsperandoHorario(
        WhatsappConversation $conversation,
        ?string $selectedOptionId,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        $company = $this->waba->company ?? \App\Models\Company::find($this->waba->company_id);
        $context = $conversation->context_json ?? [];
        $dateStr = $context['date'] ?? null;

        if ($dateStr && $selectedOptionId && preg_match('/^\d{2}:\d{2}$/', $selectedOptionId)) {
            $professionalId = $context['professional_id'] ?? null;
            $appointmentTypeId = $context['appointment_type_id'] ?? null;
            $rawSlots = $calendarService->getAvailableSlots($company, $date, $professionalId, $appointmentTypeId);
            $slots = array_values($rawSlots);

            if (in_array($selectedOptionId, $slots)) {
                $context['time'] = $selectedOptionId;
                
                $conversation->update([
                    'context_json' => $context,
                    'state' => 'esperando_dni'
                ]);

                $disclaimer = "";
                if ($company->cancellation_hours_limit > 0) {
                    $disclaimer = "\n\nRecordá que este turno no podrá cancelarse si faltan {$company->cancellation_hours_limit} horas o menos, y el monto abonado no será reembolsable en ese caso.";
                }

                $whatsAppService->sendTextMessage(
                    $this->waba,
                    $conversation->patient_phone,
                    "Perfecto, seleccionaste el día " . $date->translatedFormat('l d/m') . " a las {$selectedOptionId} hs.{$disclaimer}\n\n" .
                    "Para continuar, por favor escribe tu DNI (solo números, sin puntos ni espacios):"
                );
                return;
            }
        }

        if ($dateStr) {
            $professionalId = $context['professional_id'] ?? null;
            $appointmentTypeId = $context['appointment_type_id'] ?? null;
            $rawSlots = $calendarService->getAvailableSlots($company, $date, $professionalId, $appointmentTypeId);
            $slots = array_values($rawSlots);
            
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "Por favor, selecciona uno de los horarios válidos de la lista."
            );

            $limitedSlots = array_slice($slots, 0, 10);
            $options = array_combine($limitedSlots, $limitedSlots);

            $whatsAppService->sendListMessage(
                $this->waba,
                $conversation->patient_phone,
                "Selecciona un horario disponible para el día " . $date->translatedFormat('l d/m') . ":",
                "Ver horarios",
                "Horarios Disponibles",
                $options
            );
        } else {
            $this->handleStateInicio($conversation, $whatsAppService);
        }
    }

    /**
     * Estado: esperando_confirmacion_identidad
     */
    protected function handleStateConfirmacionIdentidad(
        WhatsappConversation $conversation,
        ?string $selectedOptionId,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        $company = $this->waba->company ?? \App\Models\Company::find($this->waba->company_id);
        $context = $conversation->context_json ?? [];
        $dni = $context['dni'] ?? null;

        if ($selectedOptionId === 'confirm_identity_yes') {
            // Reutilizar últimos datos
            $lastAppointment = Appointment::withoutGlobalScopes()
                ->where('company_id', $company->id)
                ->where('patient_dni', $dni)
                ->whereNotNull('patient_first_name')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastAppointment) {
                $context['first_name'] = $lastAppointment->patient_first_name;
                $context['last_name'] = $lastAppointment->patient_last_name;
                $context['email'] = $lastAppointment->patient_email; // Opcional
                $context['insurance'] = $lastAppointment->patient_insurance ?? 'Particular';
                
                $conversation->update(['context_json' => $context]);

                $this->createHold($conversation, $calendarService, $whatsAppService);
                return;
            } else {
                // Caso límite: el antecedente fue eliminado/reprogramado en el intermedio.
                // Redirigimos al ingreso manual de datos.
                $conversation->update(['state' => 'esperando_nombre']);
                $whatsAppService->sendTextMessage(
                    $this->waba,
                    $conversation->patient_phone,
                    "No pudimos recuperar tus datos anteriores. Por favor, escribe tu Nombre para continuar:"
                );
                return;
            }
        } elseif ($selectedOptionId === 'confirm_identity_no') {
            // Transicionar al estado intermedio de elección de corrección
            $conversation->update(['state' => 'esperando_tipo_correccion']);

            $whatsAppService->sendReplyButtons(
                $this->waba,
                $conversation->patient_phone,
                "¿Cuál es el inconveniente con tus datos?",
                [
                    'dni_typo' => 'Corregir DNI',
                    'name_wrong' => 'El DNI está bien'
                ]
            );
            return;
        }

        // Si responde texto libre, recordarle usar los botones
        $whatsAppService->sendTextMessage(
            $this->waba,
            $conversation->patient_phone,
            "Por favor, responde usando los botones que te enviamos."
        );
    }

    /**
     * Estado: esperando_tipo_correccion
     */
    protected function handleStateTipoCorreccion(
        WhatsappConversation $conversation,
        ?string $selectedOptionId,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        $company = $this->waba->company ?? \App\Models\Company::find($this->waba->company_id);
        $context = $conversation->context_json ?? [];

        if ($selectedOptionId === 'dni_typo') {
            $conversation->update(['state' => 'esperando_dni']);
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "Entendido, por favor escribe tu DNI nuevamente (solo números, sin puntos ni espacios):"
            );
            return;
        } elseif ($selectedOptionId === 'name_wrong') {
            $dni = $context['dni'] ?? null;
            
            // Obtener el nombre anterior para registrar auditoría más tarde
            $lastAppointment = Appointment::withoutGlobalScopes()
                ->where('company_id', $company->id)
                ->where('patient_dni', $dni)
                ->whereNotNull('patient_first_name')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastAppointment) {
                $context['old_name'] = "{$lastAppointment->patient_first_name} {$lastAppointment->patient_last_name}";
                $conversation->update(['context_json' => $context]);
            }

            $conversation->update(['state' => 'esperando_nombre']);
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "Entendido. Por favor, escribe tu Nombre:"
            );
            return;
        }

        // Si responde texto libre, recordarle usar los botones
        $whatsAppService->sendTextMessage(
            $this->waba,
            $conversation->patient_phone,
            "Por favor, selecciona una de las opciones usando los botones."
        );
    }

    /**
     * Estado: esperando_nombre
     */
    protected function handleStateEsperandoNombre(
        WhatsappConversation $conversation,
        string $incomingText,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        if (empty($incomingText)) {
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "Por favor, ingresa un nombre válido:"
            );
            return;
        }

        $context = $conversation->context_json ?? [];
        $context['first_name'] = $incomingText;
        
        $conversation->update([
            'context_json' => $context,
            'state' => 'esperando_apellido'
        ]);

        $whatsAppService->sendTextMessage(
            $this->waba,
            $conversation->patient_phone,
            "Gracias. Ahora escribe tu Apellido:"
        );
    }

    /**
     * Estado: esperando_apellido
     */
    protected function handleStateEsperandoApellido(
        WhatsappConversation $conversation,
        string $incomingText,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        if (empty($incomingText)) {
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "Por favor, ingresa un apellido válido:"
            );
            return;
        }

        $context = $conversation->context_json ?? [];
        $context['last_name'] = $incomingText;
        
        $conversation->update([
            'context_json' => $context,
            'state' => 'esperando_obra_social'
        ]);

        $whatsAppService->sendTextMessage(
            $this->waba,
            $conversation->patient_phone,
            "Por último antes del correo, contame tu obra social. Si no tenés, escribí Particular."
        );
    }

    /**
     * Estado: esperando_obra_social
     */
    protected function handleStateEsperandoObraSocial(
        WhatsappConversation $conversation,
        string $incomingText,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        if (empty($incomingText)) {
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "Por favor, ingresa tu obra social o escribe Particular:"
            );
            return;
        }

        $context = $conversation->context_json ?? [];
        $context['insurance'] = $incomingText;
        
        $conversation->update([
            'context_json' => $context,
            'state' => 'esperando_email'
        ]);

        $whatsAppService->sendReplyButtons(
            $this->waba,
            $conversation->patient_phone,
            "¡Gracias! Por último, escribe tu correo electrónico (o presiona Omitir si prefieres no ingresarlo):",
            [
                'skip_email' => 'Omitir'
            ]
        );
    }

    /**
     * Estado: esperando_email
     */
    protected function handleStateEsperandoEmail(
        WhatsappConversation $conversation,
        string $incomingText,
        ?string $selectedOptionId,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        $context = $conversation->context_json ?? [];

        if ($selectedOptionId === 'skip_email') {
            $context['email'] = null;
            $conversation->update(['context_json' => $context]);
            $this->createHold($conversation, $calendarService, $whatsAppService);
            return;
        }

        // Si ingresó texto libre, validamos formato de correo
        if (preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $incomingText)) {
            $context['email'] = $incomingText;
            $conversation->update(['context_json' => $context]);
            $this->createHold($conversation, $calendarService, $whatsAppService);
            return;
        }

        // En caso de correo inválido
        $whatsAppService->sendReplyButtons(
            $this->waba,
            $conversation->patient_phone,
            "El correo ingresado no es válido. Por favor, escríbelo nuevamente o presiona el botón Omitir:",
            [
                'skip_email' => 'Omitir'
            ]
        );
    }

    /**
     * Estado: esperando_dni
     */
    protected function handleStateEsperandoDni(
        WhatsappConversation $conversation,
        string $incomingText,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        $company = $this->waba->company ?? \App\Models\Company::find($this->waba->company_id);

        $normalizedDni = $this->normalizeAndValidateDni($incomingText);

        if (!$normalizedDni) {
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "El DNI ingresado no es válido (debe tener entre 7 y 8 números sin puntos ni espacios). Por favor, escríbelo nuevamente:"
            );
            return;
        }

        $context = $conversation->context_json ?? [];
        $context['dni'] = $normalizedDni; // Guardar siempre la versión normalizada
        $conversation->update(['context_json' => $context]);

        // Validar restricción de rebooking temprano por DNI
        $dateStr = $context['date'] ?? null;
        $timeStr = $context['time'] ?? null;
        if ($dateStr && $timeStr) {
            $newAppointmentDateTime = Carbon::parse($dateStr . ' ' . $timeStr);
            if ($calendarService->hasConflictingRecentAppointment($company, $normalizedDni, $newAppointmentDateTime)) {
                $whatsAppService->sendTextMessage(
                    $this->waba,
                    $conversation->patient_phone,
                    "Ya tenés un turno reservado cerca de esta fecha/horario. Según la política de " . ($company->professional_name ?? $company->name) . ", necesitás esperar al menos {$company->same_patient_rebooking_hours} horas desde tu turno anterior para reservar uno nuevo. Si necesitás coordinar algo puntual, comunicate directamente con el consultorio."
                );
                $this->handleStateInicio($conversation, $whatsAppService);
                return;
            }
        }

        // Buscar datos de reservas anteriores usando el DNI (Multi-tenant)
        $lastAppointment = Appointment::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('patient_dni', $normalizedDni)
            ->whereNotNull('patient_first_name')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastAppointment) {
            $conversation->update(['state' => 'esperando_confirmacion_identidad']);

            $whatsAppService->sendReplyButtons(
                $this->waba,
                $conversation->patient_phone,
                "Encontramos un turno anterior a nombre de {$lastAppointment->patient_first_name} {$lastAppointment->patient_last_name}.\n\n¿Sos vos?",
                [
                    'confirm_identity_yes' => 'Sí, soy yo',
                    'confirm_identity_no' => 'No, no soy yo'
                ]
            );
        } else {
            $conversation->update(['state' => 'esperando_nombre']);
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "Para confirmar la reserva, por favor escribe tu Nombre:"
            );
        }
    }

    /**
     * Creación del Bloqueo Temporal (Hold) de 15 minutos
     */
    protected function createHold(
        WhatsappConversation $conversation,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        $company = $this->waba->company ?? \App\Models\Company::find($this->waba->company_id);
        $context = $conversation->context_json ?? [];
        
        $dateStr = $context['date'] ?? null;
        $timeStr = $context['time'] ?? null;

        if (!$dateStr || !$timeStr) {
            $this->handleStateInicio($conversation, $whatsAppService);
            return;
        }

        $date = Carbon::parse($dateStr);

        try {
            // 1. Revalidar disponibilidad preventiva
            $professionalId = $context['professional_id'] ?? null;
            $appointmentTypeId = $context['appointment_type_id'] ?? null;
            $rawSlots = $calendarService->getAvailableSlots($company, $date, $professionalId, $appointmentTypeId);
            $slots = array_values($rawSlots);
 
            if (!in_array($timeStr, $slots)) {
                throw new \Exception('slot_taken');
            }
 
            // 2. Crear la cita (QueryException lanzará SQLSTATE 23000 si hay colisión paralela concurrente)
            $appointment = Appointment::create([
                'company_id' => $company->id,
                'professional_id' => $professionalId,
                'appointment_type_id' => $appointmentTypeId,
                'date' => $dateStr,
                'time' => $timeStr,
                'patient_first_name' => $context['first_name'],
                'patient_last_name' => $context['last_name'],
                'patient_phone' => $conversation->patient_phone,
                'patient_email' => $context['email'] ?? null,
                'patient_dni' => $context['dni'],
                'patient_insurance' => $context['insurance'] ?? 'Particular',
                'status' => 'pending_payment',
                'source' => 'whatsapp',
                'lock_token' => (string) Str::uuid(),
                'locked_until' => now()->addMinutes(15),
                'cancel_token' => Str::random(64),
            ]);

            // 3. Disparar Job de expiración diferido por 15 minutos
            ReleaseExpiredSlotHold::dispatch($appointment)->delay(now()->addMinutes(15));

            // Log de auditoría si el paciente corrigió su nombre/apellido asociado al DNI
            if (isset($context['old_name'])) {
                Log::channel('whatsapp')->warning('whatsapp_dni_name_correction', [
                    'dni' => $appointment->patient_dni,
                    'old_name' => $context['old_name'],
                    'new_name' => "{$appointment->patient_first_name} {$appointment->patient_last_name}"
                ]);
            }

            // 4. Decidir cobro por Mercado Pago
            $mpService = app(\App\Services\MercadoPagoService::class);

            if ($mpService->shouldCharge($company)) {
                $paymentLink = $mpService->createPreferenceForAppointment($appointment);

                if ($paymentLink) {
                    $conversation->update([
                        'state' => 'finalizado',
                        'context_json' => null
                    ]);

                    $whatsAppService->sendTextMessage(
                        $this->waba,
                        $conversation->patient_phone,
                        "💳 Para confirmar tu turno, completá el pago acá: {$paymentLink}\n\n" .
                        "Monto: \${$company->consultation_price}\n\n" .
                        "Tenés 15 minutos antes de que se libere el horario."
                    );
                } else {
                    // Falló la creación de preferencia
                    $appointment->delete();

                    $conversation->update([
                        'state' => 'inicio',
                        'context_json' => null
                    ]);

                    $whatsAppService->sendTextMessage(
                        $this->waba,
                        $conversation->patient_phone,
                        "Lo sentimos, hubo un problema al generar el enlace de pago. Por favor, inténtalo de nuevo más tarde."
                    );
                    $this->handleStateInicio($conversation, $whatsAppService);
                }
            } else {
                // Si es gratuito / sin Mercado Pago
                $appointment->update([
                    'status' => 'active',
                    'payment_method' => 'free'
                ]);

                $conversation->update([
                    'state' => 'finalizado',
                    'context_json' => null
                ]);

                $whatsAppService->sendTextMessage(
                    $this->waba,
                    $conversation->patient_phone,
                    "¡Turno reservado exitosamente!\n\n" .
                    "Tu turno para el día " . $date->translatedFormat('l d/m') . " a las {$timeStr} hs ha sido confirmado."
                );
            }

        } catch (\Illuminate\Database\QueryException $e) {
            // Capturar la violación de la restricción única (SQLSTATE 23000 o código de error MySQL 1062)
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), '1062')) {
                Log::channel('whatsapp')->warning('whatsapp_hold_collision_detected', [
                    'recipient' => $conversation->patient_phone,
                    'date' => $dateStr,
                    'time' => $timeStr
                ]);

                $conversation->update([
                    'state' => 'inicio',
                    'context_json' => null
                ]);

                $whatsAppService->sendTextMessage(
                    $this->waba,
                    $conversation->patient_phone,
                    "Lo sentimos, el horario {$timeStr} hs para el día " . $date->translatedFormat('d/m') . " acaba de ser ocupado. Por favor, selecciona otro día disponible:"
                );

                $this->handleStateMostrarDisponibilidad($conversation, $calendarService, $whatsAppService);
            } else {
                throw $e;
            }
        } catch (\Exception $e) {
            // Falla de la revalidación preventiva o lógica general
            Log::channel('whatsapp')->warning('whatsapp_hold_preventive_failed', [
                'recipient' => $conversation->patient_phone,
                'date' => $dateStr,
                'time' => $timeStr,
                'error' => $e->getMessage()
            ]);

            $conversation->update([
                'state' => 'inicio',
                'context_json' => null
            ]);

            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "Lo sentimos, el horario {$timeStr} hs para el día " . $date->translatedFormat('d/m') . " acaba de ser ocupado. Por favor, selecciona otro día disponible:"
            );

            $this->handleStateMostrarDisponibilidad($conversation, $calendarService, $whatsAppService);
        }
    }

    /**
     * Estado: cancelacion_esperando_dni
     */
    protected function handleStateCancelacionEsperandoDni(
        WhatsappConversation $conversation,
        string $incomingText,
        WhatsAppService $whatsAppService
    ) {
        $company = $this->waba->company ?? \App\Models\Company::find($this->waba->company_id);

        $normalizedDni = $this->normalizeAndValidateDni($incomingText);

        if (!$normalizedDni) {
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "El DNI ingresado no es válido (debe tener entre 7 y 8 números sin puntos ni espacios). Por favor, escríbelo nuevamente:"
            );
            return;
        }

        // Buscar turnos futuros activos/pending_payment con control de seguridad por teléfono
        $now = now();
        $appointments = Appointment::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('patient_dni', $normalizedDni)
            ->where('patient_phone', $conversation->patient_phone)
            ->whereIn('status', ['active', 'pending_payment'])
            ->where(function ($query) use ($now) {
                $query->where('date', '>', $now->toDateString())
                      ->orWhere(function ($q) use ($now) {
                          $q->where('date', '=', $now->toDateString())
                            ->where('time', '>', $now->toTimeString());
                      });
            })
            ->get();

        if ($appointments->isEmpty()) {
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "No encontramos turnos registrados con estos datos desde este número de WhatsApp. Si creés que es un error, podés acercarte personalmente a " . 
                trim(($company->address ?? '') . ', ' . ($company->city ?? '')) . "."
            );

            // Resetear a inicio
            $conversation->update([
                'state' => 'inicio',
                'context_json' => null
            ]);
            $this->handleStateInicio($conversation, $whatsAppService);
            return;
        }

        // Guardar el DNI en context_json para poder repetir la consulta fresca al volver
        $context = $conversation->context_json ?? [];
        $context['dni'] = $normalizedDni;
        $conversation->update([
            'context_json' => $context,
            'state' => 'cancelacion_seleccion_turno'
        ]);

        // Construir opciones del List Message
        $options = [];
        foreach ($appointments as $app) {
            $date = Carbon::parse($app->date);
            $options[$app->id] = ucfirst($date->translatedFormat('D d/m')) . ' - ' . substr($app->time, 0, 5);
        }

        $whatsAppService->sendListMessage(
            $this->waba,
            $conversation->patient_phone,
            "Encontramos los siguientes turnos a tu nombre. Por favor, selecciona el que deseas cancelar de la lista:",
            "Ver turnos",
            "Tus Turnos",
            $options
        );
    }

    /**
     * Estado: cancelacion_seleccion_turno
     */
    protected function handleStateCancelacionSeleccionTurno(
        WhatsappConversation $conversation,
        ?string $selectedOptionId,
        WhatsAppService $whatsAppService
    ) {
        $company = $this->waba->company ?? \App\Models\Company::find($this->waba->company_id);

        if (!$selectedOptionId) {
            // Re-enviar la lista si no seleccionó una opción válida
            $context = $conversation->context_json ?? [];
            $dni = $context['dni'] ?? null;

            if ($dni) {
                // Volvemos a consultar para armar la lista fresca
                $now = now();
                $appointments = Appointment::withoutGlobalScopes()
                    ->where('company_id', $company->id)
                    ->where('patient_dni', $dni)
                    ->where('patient_phone', $conversation->patient_phone)
                    ->whereIn('status', ['active', 'pending_payment'])
                    ->where(function ($query) use ($now) {
                        $query->where('date', '>', $now->toDateString())
                              ->orWhere(function ($q) use ($now) {
                                  $q->where('date', '=', $now->toDateString())
                                    ->where('time', '>', $now->toTimeString());
                              });
                    })
                    ->get();

                if (!$appointments->isEmpty()) {
                    $options = [];
                    foreach ($appointments as $app) {
                        $date = Carbon::parse($app->date);
                        $options[$app->id] = ucfirst($date->translatedFormat('D d/m')) . ' - ' . substr($app->time, 0, 5);
                    }

                    $whatsAppService->sendListMessage(
                        $this->waba,
                        $conversation->patient_phone,
                        "Por favor, selecciona uno de los turnos de la lista para continuar:",
                        "Ver turnos",
                        "Tus Turnos",
                        $options
                    );
                    return;
                }
            }

            // Si por algún motivo no hay DNI o turnos, resetear
            $conversation->update([
                'state' => 'inicio',
                'context_json' => null
            ]);
            $this->handleStateInicio($conversation, $whatsAppService);
            return;
        }

        // Revalidar que el Appointment exista y pertenezca al mismo company_id + patient_phone
        $appointment = Appointment::withoutGlobalScopes()
            ->where('id', $selectedOptionId)
            ->where('company_id', $company->id)
            ->where('patient_phone', $conversation->patient_phone)
            ->first();

        if (!$appointment) {
            $whatsAppService->sendTextMessage(
                $this->waba,
                $conversation->patient_phone,
                "El turno seleccionado no es válido. Por favor, selecciona uno de la lista."
            );
            return;
        }

        // Guardar el turno seleccionado en context_json
        $context = $conversation->context_json ?? [];
        $context['cancelling_appointment_id'] = $appointment->id;
        
        $conversation->update([
            'context_json' => $context,
            'state' => 'cancelacion_confirmacion'
        ]);

        // Mostrar resumen y preguntar confirmación
        $date = Carbon::parse($appointment->date);
        $summary = "Resumen del turno a cancelar:\n\n" .
                   "📅 Fecha: " . ucfirst($date->translatedFormat('l d/m')) . "\n" .
                   "⏰ Hora: " . substr($appointment->time, 0, 5) . " hs\n" .
                   "👤 Profesional/Empresa: " . ($company->professional_name ?? $company->name) . "\n" .
                   "🪪 DNI: " . $appointment->patient_dni . "\n" .
                   "👤 Paciente: " . $appointment->patient_first_name . " " . $appointment->patient_last_name;

        $whatsAppService->sendReplyButtons(
            $this->waba,
            $conversation->patient_phone,
            "{$summary}\n\n¿Confirmás que querés cancelar este turno?",
            [
                'confirm_cancel_yes' => 'Sí, cancelar',
                'confirm_cancel_no' => 'No, mantener'
            ]
        );
    }

    /**
     * Estado: cancelacion_confirmacion
     */
    protected function handleStateCancelacionConfirmacion(
        WhatsappConversation $conversation,
        ?string $selectedOptionId,
        CalendarService $calendarService,
        WhatsAppService $whatsAppService
    ) {
        $company = $this->waba->company ?? \App\Models\Company::find($this->waba->company_id);
        $context = $conversation->context_json ?? [];
        $appointmentId = $context['cancelling_appointment_id'] ?? null;
        $dni = $context['dni'] ?? null;

        if (!$appointmentId || !$dni) {
            $conversation->update([
                'state' => 'inicio',
                'context_json' => null
            ]);
            $this->handleStateInicio($conversation, $whatsAppService);
            return;
        }

        if ($selectedOptionId === 'confirm_cancel_no') {
            // Consultamos la base de datos fresca para ver cuántos turnos futuros quedan
            $now = now();
            $appointments = Appointment::withoutGlobalScopes()
                ->where('company_id', $company->id)
                ->where('patient_dni', $dni)
                ->where('patient_phone', $conversation->patient_phone)
                ->whereIn('status', ['active', 'pending_payment'])
                ->where(function ($query) use ($now) {
                    $query->where('date', '>', $now->toDateString())
                          ->orWhere(function ($q) use ($now) {
                              $q->where('date', '=', $now->toDateString())
                                ->where('time', '>', $now->toTimeString());
                          });
                })
                ->get();

            if ($appointments->count() > 1) {
                // Volver a cancelacion_seleccion_turno y re-enviar la lista fresca
                $conversation->update([
                    'state' => 'cancelacion_seleccion_turno'
                ]);

                $options = [];
                foreach ($appointments as $app) {
                    $date = Carbon::parse($app->date);
                    $options[$app->id] = ucfirst($date->translatedFormat('D d/m')) . ' - ' . substr($app->time, 0, 5);
                }

                $whatsAppService->sendListMessage(
                    $this->waba,
                    $conversation->patient_phone,
                    "Entendido, no cancelamos ese turno. Aquí tienes la lista de tus turnos nuevamente si deseas seleccionar otro:",
                    "Ver turnos",
                    "Tus Turnos",
                    $options
                );
            } else {
                // Solo había uno o ninguno en total
                $whatsAppService->sendTextMessage(
                    $this->waba,
                    $conversation->patient_phone,
                    "Entendido, no se realizó ninguna cancelación."
                );

                $conversation->update([
                    'state' => 'inicio',
                    'context_json' => null
                ]);
                $this->handleStateInicio($conversation, $whatsAppService);
            }
            return;
        }

        if ($selectedOptionId === 'confirm_cancel_yes') {
            $appointment = Appointment::withoutGlobalScopes()
                ->where('id', $appointmentId)
                ->where('company_id', $company->id)
                ->where('patient_phone', $conversation->patient_phone)
                ->first();

            if (!$appointment) {
                $whatsAppService->sendTextMessage(
                    $this->waba,
                    $conversation->patient_phone,
                    "El turno a cancelar ya no es válido o ya fue cancelado."
                );
                
                $conversation->update([
                    'state' => 'inicio',
                    'context_json' => null
                ]);
                $this->handleStateInicio($conversation, $whatsAppService);
                return;
            }

            // Calcular horas de diferencia
            $appointmentDateTime = Carbon::parse($appointment->date . ' ' . $appointment->time);
            
            // Si diffInHours >= limit es verdadero, se puede cancelar (con 0 siempre es verdadero)
            if ($appointmentDateTime->diffInHours(now()) >= $company->cancellation_hours_limit) {
                // Proceder a cancelar
                $appointment->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancellation_reason' => 'Cancelado por el paciente vía WhatsApp'
                ]);

                $date = Carbon::parse($appointment->date);
                $whatsAppService->sendTextMessage(
                    $this->waba,
                    $conversation->patient_phone,
                    "✅ El turno del día " . $date->translatedFormat('d/m') . " a las " . substr($appointment->time, 0, 5) . " hs fue cancelado exitosamente y el horario ha sido liberado."
                );

                $conversation->update([
                    'state' => 'inicio',
                    'context_json' => null
                ]);
                $this->handleStateInicio($conversation, $whatsAppService);
            } else {
                // Fuera del límite permitido (dentro del rango no cancelable)
                $whatsAppService->sendTextMessage(
                    $this->waba,
                    $conversation->patient_phone,
                    "Lo sentimos, no es posible cancelar este turno porque faltan menos de {$company->cancellation_hours_limit} horas. El monto abonado no es reembolsable.\n\n" .
                    "Si deseas, puedes solicitar un nuevo turno de la lista."
                );

                // Redirigir a menu_new_appointment (mostrar disponibilidad)
                $this->handleStateMostrarDisponibilidad($conversation, $calendarService, $whatsAppService);
            }
            return;
        }

        // Texto libre: reenviar confirmación
        $whatsAppService->sendReplyButtons(
            $this->waba,
            $conversation->patient_phone,
            "Por favor, confirma si deseas cancelar el turno:",
            [
                'confirm_cancel_yes' => 'Sí, cancelar',
                'confirm_cancel_no' => 'No, mantener'
            ]
        );
    }

    /**
     * Normaliza y valida un DNI de Argentina.
     * Retorna el DNI normalizado de 7 u 8 dígitos, o null si es inválido.
     */
    private function normalizeAndValidateDni(string $incomingText): ?string
    {
        // 1. Normalizar el DNI (removiendo puntos, guiones y espacios)
        $normalizedDni = preg_replace('/[.\-\s]/', '', $incomingText);

        // 2. Validar formato (debe tener entre 7 y 8 números)
        if (!preg_match('/^\d{7,8}$/', $normalizedDni)) {
            return null;
        }

        return $normalizedDni;
    }
}
