<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentTypeRequest;
use App\Http\Requests\UpdateAppointmentTypeRequest;
use App\Models\AppointmentType;
use Illuminate\Http\Request;

class AppointmentTypeController extends Controller
{
    public function index()
    {
        $appointmentTypes = AppointmentType::orderBy('id', 'desc')->get();
        return view('admin.appointment_types.index', compact('appointmentTypes'));
    }

    public function create()
    {
        return view('admin.appointment_types.create');
    }

    public function store(StoreAppointmentTypeRequest $request)
    {
        $data = $request->validated();
        $data['company_id'] = auth()->user()->company_id;
        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : true;

        AppointmentType::create($data);

        return redirect()->route('admin.appointment-types.index')
            ->with('success', 'Tipo de turno creado con éxito.');
    }

    public function edit(AppointmentType $appointmentType)
    {
        return view('admin.appointment_types.edit', compact('appointmentType'));
    }

    public function update(UpdateAppointmentTypeRequest $request, AppointmentType $appointmentType)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : false;

        $appointmentType->update($data);

        return redirect()->route('admin.appointment-types.index')
            ->with('success', 'Tipo de turno actualizado con éxito.');
    }

    public function destroy(AppointmentType $appointmentType)
    {
        // 1. Validar que no tenga turnos futuros activos/pendientes
        $hasFutureAppointments = $appointmentType->appointments()
            ->whereDate('date', '>=', now()->format('Y-m-d'))
            ->whereIn('status', ['active', 'pending_payment', 'rescheduled'])
            ->exists();

        if ($hasFutureAppointments) {
            return back()->with('error', 'No se puede eliminar este tipo de turno porque existen turnos programados a futuro de esta categoría. Primero debes cancelarlos o reprogramarlos.');
        }

        $appointmentType->delete();

        return redirect()->route('admin.appointment-types.index')
            ->with('success', 'Tipo de turno eliminado con éxito.');
    }
}
