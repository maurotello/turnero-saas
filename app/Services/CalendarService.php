<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\BlockedDay;
use App\Models\Company;
use App\Models\ScheduleSetting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CalendarService
{
    /**
     * Obtiene los horarios disponibles para una fecha específica.
     */
    public function getAvailableSlots(Company $company, Carbon $date, ?int $professionalId = null, ?int $appointmentTypeId = null)
    {
        // 1. Obtener si el día actual está bloqueado (general o para el profesional específico)
        $blocked = BlockedDay::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->whereDate('date', $date)
            ->where(function ($query) use ($professionalId) {
                $query->whereNull('professional_id');
                if ($professionalId) {
                    $query->orWhere('professional_id', $professionalId);
                }
            })
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        // Si no se especifica tipo de turno, obtener el primero activo
        if (!$appointmentTypeId) {
            $appointmentTypeId = $company->appointmentTypes()->where('is_active', true)->value('id');
        }

        // 2. Obtener la configuración semanal para el día actual y el tipo de turno específico
        $daySettings = ScheduleSetting::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('day_of_week', $date->dayOfWeek)
            ->where('is_active', true)
            ->where('professional_id', $professionalId)
            ->where('appointment_type_id', $appointmentTypeId)
            ->get();

        // Fallback a configuración global si el profesional no tiene una específica
        if ($daySettings->isEmpty() && $professionalId) {
            $daySettings = ScheduleSetting::withoutGlobalScopes()
                ->where('company_id', $company->id)
                ->where('day_of_week', $date->dayOfWeek)
                ->where('is_active', true)
                ->whereNull('professional_id')
                ->where('appointment_type_id', $appointmentTypeId)
                ->get();
        }

        // 3. Obtener turnos ocupados o reservados temporalmente para este profesional
        $taken = Appointment::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->whereDate('date', $date)
            ->where('professional_id', $professionalId)
            ->where(function ($query) {
                $query->whereIn('status', ['active', 'rescheduled'])
                      ->orWhere('status', 'pending_payment')
                      ->orWhere(function ($q) {
                           $q->whereNotNull('lock_token')
                             ->where('locked_until', '>', now());
                       });
            })
            ->pluck('time')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        // 4. Calcular usando la lógica unificada
        return $this->calculateSlotsForDay($date, $daySettings, $blocked, $taken);
    }

    /**
     * Obtiene los primeros N días hábiles disponibles dentro de un rango en solo 3 consultas a la BD.
     */
    public function getAvailableDaysInRange(Company $company, Carbon $startDate, Carbon $endDate, ?int $professionalId = null, ?int $appointmentTypeId = null, int $limit = 10): array
    {
        // Si no se especifica tipo de turno, obtener el primero activo
        if (!$appointmentTypeId) {
            $appointmentTypeId = $company->appointmentTypes()->where('is_active', true)->value('id');
        }

        // 1. Obtener configuración semanal
        $settingsQuery = ScheduleSetting::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('appointment_type_id', $appointmentTypeId)
            ->where('is_active', true);

        if ($professionalId) {
            $hasProfSettings = (clone $settingsQuery)->where('professional_id', $professionalId)->exists();
            if ($hasProfSettings) {
                $settingsQuery->where('professional_id', $professionalId);
            } else {
                $settingsQuery->whereNull('professional_id');
            }
        } else {
            $settingsQuery->whereNull('professional_id');
        }

        $settings = $settingsQuery->get()->groupBy('day_of_week');

        if ($settings->isEmpty()) {
            return [];
        }

        // 2. Obtener días bloqueados (globales + específicos del profesional)
        $blockedDates = BlockedDay::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where(function ($query) use ($professionalId) {
                $query->whereNull('professional_id');
                if ($professionalId) {
                    $query->orWhere('professional_id', $professionalId);
                }
            })
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        // 3. Obtener turnos asignados a este profesional
        $appointments = Appointment::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('professional_id', $professionalId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where(function ($query) {
                $query->whereIn('status', ['active', 'rescheduled'])
                      ->orWhere('status', 'pending_payment')
                      ->orWhere(function ($q) {
                           $q->whereNotNull('lock_token')
                             ->where('locked_until', '>', now());
                       });
            })
            ->get()
            ->groupBy(fn($app) => Carbon::parse($app->date)->format('Y-m-d'));

        $availableDays = [];
        $current = clone $startDate;

        while ($current->lte($endDate) && count($availableDays) < $limit) {
            $dateStr = $current->format('Y-m-d');
            $daySettings = $settings->get($current->dayOfWeek);

            $takenForDate = $appointments->get($dateStr, collect())
                ->pluck('time')
                ->map(fn($t) => Carbon::parse($t)->format('H:i'))
                ->toArray();

            // Usar la misma lógica unificada
            $slots = $this->calculateSlotsForDay($current, $daySettings, $blockedDates, $takenForDate);

            if (count($slots) > 0) {
                $availableDays[$dateStr] = $current->translatedFormat('D d/m');
            }

            $current->addDay();
        }

        return $availableDays;
    }

    /**
     * Lógica Unificada: Calcula qué turnos quedan libres en una fecha determinada.
     */
    private function calculateSlotsForDay(
        Carbon $date,
        $daySettings,
        array $blockedDates,
        array $takenSlotsForDate
    ): array {
        $dateStr = $date->format('Y-m-d');

        // 1. Validar si el día está bloqueado
        if (in_array($dateStr, $blockedDates)) {
            return [];
        }

        if (!$daySettings) {
            return [];
        }

        // Convertir a colección si es un solo modelo o array
        if ($daySettings instanceof ScheduleSetting) {
            $daySettings = collect([$daySettings]);
        } elseif (is_array($daySettings)) {
            $daySettings = collect($daySettings);
        }

        // 2. Validar si hay horarios de atención activos
        $activeSettings = $daySettings->filter(fn($s) => $s->is_active);
        if ($activeSettings->isEmpty()) {
            return [];
        }

        // 3. Generar slots teóricos para cada jornada/intervalo
        $allSlots = [];
        foreach ($activeSettings as $setting) {
            $startTime = Carbon::parse($setting->start_time);
            $endTime = Carbon::parse($setting->end_time);
            $duration = $setting->slot_duration;

            $current = clone $startTime;
            while ($current->copy()->addMinutes($duration)->lte($endTime)) {
                $allSlots[] = $current->format('H:i');
                $current->addMinutes($duration);
            }
        }

        // Ordenar y quitar duplicados cronológicamente
        $allSlots = array_values(array_unique($allSlots));
        sort($allSlots);

        // 4. Filtrar slots teóricos contra turnos tomados y hora actual si es hoy
        return array_filter($allSlots, function ($slot) use ($takenSlotsForDate, $date) {
            if ($date->isToday()) {
                if (Carbon::parse($slot)->lt(now())) {
                    return false;
                }
            }
            return !in_array($slot, $takenSlotsForDate);
        });
    }

    /**
     * Retorno antiguo para Blade (mantiene compatibilidad intacta)
     */
    public function getMonthAvailability(Company $company, Carbon $month, ?int $professionalId = null, ?int $appointmentTypeId = null)
    {
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        $availability = [];
        foreach ($period as $date) {
            $slots = $this->getAvailableSlots($company, $date, $professionalId, $appointmentTypeId);
            $availability[$date->format('Y-m-d')] = [
                'has_slots' => count($slots) > 0,
                'count' => count($slots)
            ];
        }

        return $availability;
    }

    /**
     * Valida si el paciente tiene turnos activos o pendientes que entren en conflicto
     * por rango de horas respecto a un nuevo turno.
     */
    public function hasConflictingRecentAppointment(Company $company, string $patientDni, Carbon $newAppointmentDateTime): bool
    {
        if ((int) $company->same_patient_rebooking_hours === 0) {
            return false;
        }

        // Buscar turnos activos o con hold del mismo paciente (Multi-tenant)
        $appointments = Appointment::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('patient_dni', $patientDni)
            ->whereIn('status', ['active', 'pending_payment', 'rescheduled'])
            ->get();

        foreach ($appointments as $appointment) {
            // Combinar la fecha del turno y la hora
            $appointmentDateStr = $appointment->date->format('Y-m-d');
            $appointmentDateTime = Carbon::parse($appointmentDateStr . ' ' . $appointment->time);

            // Calcular diferencia absoluta en horas
            $diffInHours = $appointmentDateTime->diffInMinutes($newAppointmentDateTime) / 60.0;

            if ($diffInHours < $company->same_patient_rebooking_hours) {
                return true;
            }
        }

        return false;
    }
}
