<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Company;
use App\Services\CalendarService;
use App\Jobs\ReleaseExpiredSlotHold;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Services\MercadoPagoService;
use App\Models\WhatsappBusinessAccount;
use App\Services\WhatsAppService;

class BookingController extends Controller
{
    protected $calendarService;
    protected $mpService;

    public function __construct(CalendarService $calendarService, MercadoPagoService $mpService)
    {
        $this->calendarService = $calendarService;
        $this->mpService = $mpService;
    }

    public function show($slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        $professionals = $company->professionals()->where('is_active', true)->get();
        $appointmentTypes = $company->appointmentTypes()->where('is_active', true)->get();
        return view('booking.show', compact('company', 'professionals', 'appointmentTypes'));
    }

    public function getSlots(Request $request, $slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        $date = Carbon::parse($request->date);
        $professionalId = $request->query('professional_id');
        $appointmentTypeId = $request->query('appointment_type_id');
        
        $slots = $this->calendarService->getAvailableSlots($company, $date, $professionalId, $appointmentTypeId);
        
        return response()->json([
            'slots' => array_values($slots),
            'date_formatted' => $date->translatedFormat('l d \d\e F')
        ]);
    }

    public function getMonthAvailability(Request $request, $slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        $monthStr = $request->query('month', now()->format('Y-m'));
        $month = Carbon::parse($monthStr . '-01');
        $professionalId = $request->query('professional_id');
        $appointmentTypeId = $request->query('appointment_type_id');
        
        $availability = $this->calendarService->getMonthAvailability($company, $month, $professionalId, $appointmentTypeId);
        
        return response()->json($availability);
    }

    public function lockSlot(Request $request, $slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        $date = Carbon::parse($request->date);
        $time = $request->time;
        $professionalId = $request->professional_id;
        $appointmentTypeId = $request->appointment_type_id;
        
        $available = $this->calendarService->getAvailableSlots($company, $date, $professionalId, $appointmentTypeId);
        
        if (!in_array($time, $available)) {
            return response()->json(['success' => false, 'message' => 'El horario ya no está disponible.'], 422);
        }

        $lockToken = Str::random(32);
        
        Appointment::where('company_id', $company->id)
            ->whereNotNull('lock_token')
            ->where('locked_until', '<', now())
            ->delete();

        return response()->json([
            'success' => true,
            'lock_token' => $lockToken
        ]);
    }

    public function confirm(Request $request, $slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        
        if (!auth('patient')->check()) {
            return redirect()->route('booking.login', $slug)->with('error', 'Debes iniciar sesión para confirmar tu turno.');
        }

        $patient = auth('patient')->user();

        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'professional_id' => 'required|exists:professionals,id',
            'appointment_type_id' => 'required|exists:appointment_types,id',
            'patient_first_name' => 'required|string|max:255',
            'patient_last_name' => 'required|string|max:255',
            'patient_email' => 'required|email|max:255',
            'patient_phone' => 'required|string|max:20',
            'patient_dni' => 'required|string|max:20',
            'patient_insurance' => 'required|string|max:255',
        ]);

        $date = Carbon::parse($request->date);
        $available = $this->calendarService->getAvailableSlots($company, $date, $request->professional_id, $request->appointment_type_id);
        
        if (!in_array($request->time, $available)) {
            return back()->withErrors(['time' => 'El horario seleccionado acaba de ser ocupado. Por favor elige otro.'])->withInput();
        }

        $newAppointmentDateTime = Carbon::parse($request->date . ' ' . $request->time);
        if ($this->calendarService->hasConflictingRecentAppointment($company, $request->patient_dni, $newAppointmentDateTime)) {
            return back()->withErrors([
                'patient_dni' => "Ya tenés un turno reservado cerca de esta fecha/horario. Según la política de " . ($company->professional_name ?? $company->name) . ", necesitás esperar al menos {$company->same_patient_rebooking_hours} horas desde tu turno anterior para reservar uno nuevo. Si necesitás coordinar algo puntual, comunicate directamente con el consultorio."
            ])->withInput();
        }

        $hasMp = $this->mpService->shouldCharge($company);
        $status = $hasMp ? 'pending_payment' : 'active';

        $appointment = Appointment::create([
            'company_id' => $company->id,
            'patient_id' => $patient->id,
            'professional_id' => $request->professional_id,
            'appointment_type_id' => $request->appointment_type_id,
            'date' => $request->date,
            'time' => $request->time,
            'patient_first_name' => $request->patient_first_name,
            'patient_last_name' => $request->patient_last_name,
            'patient_email' => $request->patient_email,
            'patient_phone' => $request->patient_phone,
            'patient_dni' => $request->patient_dni,
            'patient_insurance' => $request->patient_insurance,
            'status' => $status,
            'cancel_token' => Str::random(64),
        ]);

        if ($hasMp) {
            ReleaseExpiredSlotHold::dispatch($appointment)->delay(now()->addMinutes(60));

            $link = $this->mpService->createPreferenceForAppointment($appointment);
            if ($link) {
                return redirect($link);
            } else {
                $appointment->delete();
                return back()->withErrors(['payment' => 'Hubo un problema al conectar con la pasarela de pago. Por favor intente más tarde.'])->withInput();
            }
        }

        // Enviar Email con PDF adjunto (solo si es reserva directa/gratuita)
        if (!empty($appointment->patient_email)) {
            \Illuminate\Support\Facades\Mail::to($appointment->patient_email)
                ->send(new \App\Mail\AppointmentConfirmation($appointment));
        }
        
        return redirect()->route('booking.success', [$company->slug, $appointment->id]);
    }

    public function paymentSuccess(Request $request, $slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        $appointmentId = $request->query('appointment_id');
        $paymentId = $request->query('payment_id');

        $appointment = Appointment::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('id', $appointmentId)
            ->firstOrFail();

        if ($appointment->status === 'active') {
            return redirect()->route('booking.success', [$company->slug, $appointment->id]);
        }

        if ($appointment->status === 'cancelled') {
            return redirect()->route('booking.show', $company->slug)
                ->with('error', 'El pago fue rechazado. Podés intentar reservar de nuevo con otro medio de pago.');
        }

        // Validar el pago llamando a Mercado Pago
        if ($paymentId && $company->mp_access_token) {
            try {
                $response = Http::withToken($company->mp_access_token)
                    ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

                if ($response->successful()) {
                    $paymentData = $response->json();
                    
                    if ((int) $paymentData['external_reference'] === (int) $appointment->id) {
                        $status = $paymentData['status'];

                        if ($status === 'approved' && $appointment->status === 'pending_payment') {
                            // Activar el turno
                            $appointment->update([
                                'status' => 'active',
                                'payment_method' => 'mercadopago',
                                'notes' => ($appointment->notes ? $appointment->notes . "\n" : "") . "Pago confirmado vía Mercado Pago (ID: {$paymentId})."
                            ]);

                            // Enviar el email de confirmación
                            if (!empty($appointment->patient_email)) {
                                \Illuminate\Support\Facades\Mail::to($appointment->patient_email)
                                    ->send(new \App\Mail\AppointmentConfirmation($appointment));
                            }

                            // Notificar por WhatsApp
                            $dateFormatted = Carbon::parse($appointment->date)->translatedFormat('l d/m');
                            $timeFormatted = substr($appointment->time, 0, 5);
                            $companyName = $company->professional_name ?? $company->name;

                            $this->notifyPatientByWhatsApp(
                                $appointment,
                                "✅ ¡Pago confirmado y turno agendado exitosamente!\n\n" .
                                "📅 Fecha: " . ucfirst($dateFormatted) . "\n" .
                                "⏰ Hora: {$timeFormatted} hs\n" .
                                "👤 Profesional/Empresa: {$companyName}"
                            );

                            return redirect()->route('booking.success', [$company->slug, $appointment->id]);

                        } elseif ($status === 'rejected' && $appointment->status === 'pending_payment') {
                            // Caso rejected: Cancelar turno y liberar slot
                            $appointment->update([
                                'status' => 'cancelled',
                                'cancelled_at' => now(),
                                'cancellation_reason' => "Pago rechazado por Mercado Pago (ID: {$paymentId})"
                            ]);

                            $this->notifyPatientByWhatsApp(
                                $appointment,
                                "❌ Tu pago no pudo ser procesado. El horario reservado fue liberado. Si querés, podés escribirnos de nuevo para elegir un turno e intentar el pago nuevamente."
                            );

                            return redirect()->route('booking.show', $company->slug)
                                ->with('error', 'El pago fue rechazado. Podés intentar reservar de nuevo con otro medio de pago.');

                        } elseif (in_array($status, ['pending', 'in_process']) && $appointment->status === 'pending_payment') {
                            // Caso pending / in_process: Registrar nota y notificar sin cambiar status
                            $appointment->update([
                                'notes' => ($appointment->notes ? $appointment->notes . "\n" : "") . "Pago pendiente de acreditación (ID: {$paymentId})."
                            ]);

                            // Cálculo del tiempo restante del hold (corregido)
                            $minutesLeft = ($appointment->locked_until && now()->lt($appointment->locked_until))
                                ? (int) now()->diffInMinutes($appointment->locked_until)
                                : null;
                            $holdPhrase = $minutesLeft ? "te quedan aproximadamente {$minutesLeft} minutos de reserva" : "tu horario está reservado por tiempo limitado";

                            $this->notifyPatientByWhatsApp(
                                $appointment,
                                "⏳ Tu pago quedó pendiente de acreditación. Tené en cuenta que {$holdPhrase} — si el pago no se acredita antes de que se libere, vas a tener que volver a intentarlo. Te recomendamos usar tarjeta de crédito o débito para una confirmación inmediata."
                            );

                            return redirect()->route('booking.show', $company->slug)
                                ->with('warning', 'Tu pago está pendiente de acreditación. Recordá que la reserva del horario es por tiempo limitado.');
                        }
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('MP Payment Verification Exception', ['message' => $e->getMessage()]);
            }
        }

        return redirect()->route('booking.show', $company->slug)
            ->with('error', 'No se pudo verificar el pago del turno. Por favor, contacta al profesional.');
    }

    public function webhook(Request $request, $slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        
        $paymentId = $request->input('data.id') ?? $request->input('resource');
        
        if (!$paymentId || !$company->mp_access_token) {
            return response()->json(['status' => 'ignored'], 200);
        }

        try {
            $response = Http::withToken($company->mp_access_token)
                ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

            if ($response->successful()) {
                $paymentData = $response->json();
                $appointmentId = $paymentData['external_reference'] ?? null;

                if ($appointmentId) {
                    $status = $paymentData['status'];

                    $appointment = Appointment::withoutGlobalScopes()
                        ->where('company_id', $company->id)
                        ->where('id', $appointmentId)
                        ->where('status', 'pending_payment')
                        ->first();

                    if ($appointment) {
                        if ($status === 'approved') {
                            $appointment->update([
                                'status' => 'active',
                                'payment_method' => 'mercadopago',
                                'notes' => ($appointment->notes ? $appointment->notes . "\n" : "") . "Pago confirmado vía Webhook de Mercado Pago (ID: {$paymentId})."
                            ]);

                            // Enviar el email de confirmación
                            if (!empty($appointment->patient_email)) {
                                \Illuminate\Support\Facades\Mail::to($appointment->patient_email)
                                    ->send(new \App\Mail\AppointmentConfirmation($appointment));
                            }

                            // Notificar por WhatsApp
                            $dateFormatted = Carbon::parse($appointment->date)->translatedFormat('l d/m');
                            $timeFormatted = substr($appointment->time, 0, 5);
                            $companyName = $company->professional_name ?? $company->name;

                            $this->notifyPatientByWhatsApp(
                                $appointment,
                                "✅ ¡Pago confirmado y turno agendado exitosamente!\n\n" .
                                "📅 Fecha: " . ucfirst($dateFormatted) . "\n" .
                                "⏰ Hora: {$timeFormatted} hs\n" .
                                "👤 Profesional/Empresa: {$companyName}"
                            );
                        } elseif ($status === 'rejected') {
                            $appointment->update([
                                'status' => 'cancelled',
                                'cancelled_at' => now(),
                                'cancellation_reason' => "Pago rechazado por Mercado Pago (ID: {$paymentId})"
                            ]);

                            $this->notifyPatientByWhatsApp(
                                $appointment,
                                "❌ Tu pago no pudo ser procesado. El horario reservado fue liberado. Si querés, podés escribirnos de nuevo para elegir un turno e intentar el pago nuevamente."
                            );
                        } elseif (in_array($status, ['pending', 'in_process'])) {
                            $appointment->update([
                                'notes' => ($appointment->notes ? $appointment->notes . "\n" : "") . "Pago pendiente de acreditación vía Webhook de Mercado Pago (ID: {$paymentId})."
                            ]);

                            // Cálculo del tiempo restante del hold (corregido)
                            $minutesLeft = ($appointment->locked_until && now()->lt($appointment->locked_until))
                                ? (int) now()->diffInMinutes($appointment->locked_until)
                                : null;
                            $holdPhrase = $minutesLeft ? "te quedan aproximadamente {$minutesLeft} minutos de reserva" : "tu horario está reservado por tiempo limitado";

                            $this->notifyPatientByWhatsApp(
                                $appointment,
                                "⏳ Tu pago quedó pendiente de acreditación. Tené en cuenta que {$holdPhrase} — si el pago no se acredita antes de que se libere, vas a tener que volver a intentarlo. Te recomendamos usar tarjeta de crédito o débito para una confirmación inmediata."
                            );
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('MP Webhook Exception', ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['status' => 'processed'], 200);
    }

    private function notifyPatientByWhatsApp(Appointment $appointment, string $message): void
    {
        if ($appointment->source !== 'whatsapp') {
            return;
        }

        $waba = WhatsappBusinessAccount::where('company_id', $appointment->company_id)->first();
        if (!$waba) {
            return;
        }

        $whatsAppService = app(WhatsAppService::class);
        $whatsAppService->sendTextMessage($waba, $appointment->patient_phone, $message);
    }

    public function cancelForm($token)
    {
        $appointment = Appointment::withoutGlobalScopes()->where('cancel_token', $token)->firstOrFail();
        $company = $appointment->company;

        // Regla Crítica: Configurable por empresa/médico
        $canCancel = Carbon::parse($appointment->date . ' ' . $appointment->time)->diffInHours(now()) >= $company->cancellation_hours_limit;

        return view('booking.cancel', compact('appointment', 'company', 'canCancel'));
    }

    public function cancel(Request $request, $token)
    {
        $appointment = Appointment::withoutGlobalScopes()->where('cancel_token', $token)->firstOrFail();
        $company = $appointment->company;
        
        $canCancel = Carbon::parse($appointment->date . ' ' . $appointment->time)->diffInHours(now()) >= $company->cancellation_hours_limit;

        if (!$canCancel) {
            return back()->with('error', 'Lo sentimos, las cancelaciones deben realizarse con al menos ' . $company->cancellation_hours_limit . ' horas de anticipación.');
        }

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Cancelado por el paciente'
        ]);

        return redirect()->route('booking.show', $appointment->company->slug)->with('success', 'Tu turno ha sido cancelado.');
    }

    public function success($slug, Appointment $appointment)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        return view('booking.success', compact('company', 'appointment'));
    }
}
