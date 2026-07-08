<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleSetting;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function edit(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        // Determinar profesional seleccionado y aplicar restricciones por rol
        $selectedProfessionalId = null;
        if ($user->role === 'doctor') {
            $selectedProfessionalId = $user->professional_id;
        } else {
            $selectedProfessionalId = $request->query('professional_id');
            if ($selectedProfessionalId === 'global' || $selectedProfessionalId === null) {
                $selectedProfessionalId = null;
            } else {
                $selectedProfessionalId = (int) $selectedProfessionalId;
            }
        }

        // Obtener lista de profesionales si es administrador
        $professionals = collect();
        if ($user->role !== 'doctor') {
            $professionals = $company->professionals()->where('is_active', true)->get();
        }

        // Cargar configuraciones agrupadas por día
        $settingsQuery = ScheduleSetting::withoutGlobalScopes()
            ->where('company_id', $company->id);

        if ($selectedProfessionalId) {
            $settingsQuery->where('professional_id', $selectedProfessionalId);
        } else {
            $settingsQuery->whereNull('professional_id');
        }

        $settings = $settingsQuery->orderBy('day_of_week')->get()->groupBy('day_of_week');

        $appointmentTypes = $company->appointmentTypes()->where('is_active', true)->get();

        $days = [
            0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 
            3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'
        ];

        return view('admin.schedule.edit', compact('settings', 'days', 'professionals', 'selectedProfessionalId', 'appointmentTypes'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        // Validar rol
        $selectedProfessionalId = null;
        if ($user->role === 'doctor') {
            $selectedProfessionalId = $user->professional_id;
        } else {
            $selectedProfessionalId = $request->input('professional_id');
            if ($selectedProfessionalId === 'global' || $selectedProfessionalId === '') {
                $selectedProfessionalId = null;
            } else {
                $selectedProfessionalId = (int) $selectedProfessionalId;
            }
        }

        $data = $request->validate([
            'days' => 'required|array',
            'days.*.is_active' => 'nullable',
            'days.*.intervals' => 'nullable|array',
            'days.*.intervals.*.start_time' => 'required_with:days.*.intervals',
            'days.*.intervals.*.end_time' => 'required_with:days.*.intervals',
            'days.*.intervals.*.appointment_type_id' => 'required_with:days.*.intervals|integer|exists:appointment_types,id',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($company, $selectedProfessionalId, $data) {
            foreach ($data['days'] as $dayOfWeek => $values) {
                // Eliminar configuración previa del día
                ScheduleSetting::withoutGlobalScopes()
                    ->where('company_id', $company->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('professional_id', $selectedProfessionalId)
                    ->delete();

                $isActive = isset($values['is_active']);
                $intervals = $values['intervals'] ?? [];

                if ($isActive && count($intervals) > 0) {
                    foreach ($intervals as $interval) {
                        $appType = \App\Models\AppointmentType::withoutGlobalScopes()
                            ->where('company_id', $company->id)
                            ->findOrFail($interval['appointment_type_id']);

                        ScheduleSetting::create([
                            'company_id' => $company->id,
                            'professional_id' => $selectedProfessionalId,
                            'appointment_type_id' => $appType->id,
                            'day_of_week' => $dayOfWeek,
                            'start_time' => $interval['start_time'],
                            'end_time' => $interval['end_time'],
                            'slot_duration' => $appType->duration,
                            'is_active' => true,
                        ]);
                    }
                } else {
                    // Crear registro inactivo de control
                    ScheduleSetting::create([
                        'company_id' => $company->id,
                        'professional_id' => $selectedProfessionalId,
                        'day_of_week' => $dayOfWeek,
                        'start_time' => '08:00:00',
                        'end_time' => '12:00:00',
                        'slot_duration' => 30,
                        'is_active' => false,
                    ]);
                }
            }
        });

        $redirectUrl = route('admin.schedule.edit');
        if ($selectedProfessionalId) {
            $redirectUrl .= '?professional_id=' . $selectedProfessionalId;
        } else {
            $redirectUrl .= '?professional_id=global';
        }

        return redirect($redirectUrl)->with('success', 'Configuración de agenda actualizada con éxito.');
    }
}
