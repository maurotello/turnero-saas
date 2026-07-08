@extends('layouts.patient')

@section('title', 'Mis Turnos')

@section('content')
<div class="row">
    <!-- Panel superior con resumen -->
    <div class="col-12 mb-4">
        <div class="card card-custom bg-white p-4">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h3 class="fw-bold mb-1">Hola, {{ $patient->first_name }} 👋</h3>
                    <p class="text-muted small mb-0">Desde aquí podés ver el historial de tus turnos y gestionar tus reservas activas.</p>
                </div>
                <div>
                    <a href="{{ route('booking.show', $company->slug) }}" class="btn btn-primary px-4 py-2.5 fw-semibold d-inline-flex align-items-center gap-2" style="border-radius: 50px;">
                        <i class="bi bi-plus-circle"></i> Nuevo Turno
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="col-md-4 mb-4">
        <div class="card card-custom bg-white p-3 border-start border-primary border-4 h-100">
            <div class="card-body py-2">
                <span class="text-muted small text-uppercase tracking-wider fw-bold">Turnos Totales</span>
                <h2 class="fw-bold text-dark mt-1 mb-0">{{ $appointments->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card card-custom bg-white p-3 border-start border-success border-4 h-100">
            <div class="card-body py-2">
                <span class="text-muted small text-uppercase tracking-wider fw-bold">Turnos Activos</span>
                <h2 class="fw-bold text-dark mt-1 mb-0">{{ $appointments->where('status', 'active')->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card card-custom bg-white p-3 border-start border-danger border-4 h-100">
            <div class="card-body py-2">
                <span class="text-muted small text-uppercase tracking-wider fw-bold">Cancelados</span>
                <h2 class="fw-bold text-dark mt-1 mb-0">{{ $appointments->where('status', 'cancelled')->count() }}</h2>
            </div>
        </div>
    </div>

    <!-- Historial de Turnos -->
    <div class="col-12">
        <div class="card card-custom bg-white p-4">
            <div class="card-body p-0">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-clock-history text-primary-custom me-2"></i>Historial de Turnos</h5>

                @if($appointments->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-calendar2-x text-muted" style="font-size: 3rem;"></i>
                        <h6 class="text-muted mt-3 fw-medium">No tenés turnos programados todavía.</h6>
                        <a href="{{ route('booking.show', $company->slug) }}" class="btn btn-outline-primary btn-sm mt-3" style="border-radius: 50px;">
                            Reservar mi primer turno
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-top">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3 px-3">Fecha y Hora</th>
                                    <th class="py-3">Profesional / Especialidad</th>
                                    <th class="py-3">Estado</th>
                                    <th class="py-3">Método de Pago</th>
                                    <th class="py-3">Detalles</th>
                                    <th class="py-3 text-end px-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $appointment)
                                    @php
                                        // Verificar si es posible cancelar (horas límite definidas por la empresa)
                                        $appointmentDateTime = \Carbon\Carbon::parse($appointment->date->format('Y-m-d') . ' ' . $appointment->time);
                                        $canCancel = $appointmentDateTime->diffInHours(now(), false) <= -$company->cancellation_hours_limit;
                                    @endphp
                                    <tr>
                                        <td class="py-3 px-3">
                                            <span class="fw-bold text-dark d-block">
                                                {{ $appointment->date->translatedFormat('d \d\e F, Y') }}
                                            </span>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-clock me-1"></i> {{ substr($appointment->time, 0, 5) }} hs
                                            </small>
                                        </td>
                                        <td class="py-3">
                                            @if($company->professional_name)
                                                <span class="fw-medium text-dark d-block">{{ $company->professional_title }} {{ $company->professional_name }}</span>
                                                <small class="text-muted d-block">{{ $company->specialty }}</small>
                                            @else
                                                <span class="fw-medium text-dark">{{ $company->name }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if($appointment->status === 'active')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2" style="border-radius: 50px;">
                                                    <i class="bi bi-check-circle me-1"></i> Activo
                                                </span>
                                            @elseif($appointment->status === 'pending_payment')
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2" style="border-radius: 50px;">
                                                    <i class="bi bi-hourglass-split me-1"></i> Pago Pendiente
                                                </span>
                                            @elseif($appointment->status === 'cancelled')
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2" style="border-radius: 50px;">
                                                    <i class="bi bi-x-circle me-1"></i> Cancelado
                                                </span>
                                            @elseif($appointment->status === 'rescheduled')
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2" style="border-radius: 50px;">
                                                    <i class="bi bi-arrow-repeat-left me-1"></i> Reprogramado
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if($appointment->payment_method === 'mercadopago')
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2" style="border-radius: 50px;">
                                                    Mercado Pago
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if($appointment->notes)
                                                <span class="text-muted small text-truncate d-inline-block" style="max-width: 200px;" title="{{ $appointment->notes }}">
                                                    {{ $appointment->notes }}
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-end px-3">
                                            @if($appointment->status === 'active' || $appointment->status === 'pending_payment')
                                                @if($canCancel)
                                                    <a href="{{ route('booking.cancel.form', $appointment->cancel_token) }}" class="btn btn-outline-danger btn-sm px-3" style="border-radius: 50px;">
                                                        Cancelar
                                                    </a>
                                                @else
                                                    <button class="btn btn-outline-secondary btn-sm px-3" disabled title="No se puede cancelar con menos de {{ $company->cancellation_hours_limit }} horas de anticipación" style="border-radius: 50px;">
                                                        No cancelable
                                                    </button>
                                                @endif
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
