@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Dashboard</h2>
    <div class="text-muted">{{ now()->translatedFormat('l d \d\e F, Y') }}</div>
</div>

<div class="row g-4 mb-5">
    <!-- Today Stats -->
    <div class="col-md-3">
        <div class="card p-3 border-start border-primary border-4">
            <div class="text-muted small fw-bold text-uppercase">Hoy</div>
            <div class="h3 fw-bold mb-0">{{ $stats['today_count'] }}</div>
            <div class="text-muted small">Turnos programados</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-start border-success border-4">
            <div class="text-muted small fw-bold text-uppercase">Activos</div>
            <div class="h3 fw-bold mb-0">{{ $stats['pending_count'] }}</div>
            <div class="text-muted small">Total pendientes</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-start border-danger border-4">
            <div class="text-muted small fw-bold text-uppercase">Cancelados</div>
            <div class="h3 fw-bold mb-0">{{ $stats['cancelled_count'] }}</div>
            <div class="text-muted small">En el sistema</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-start border-info border-4">
            <div class="text-muted small fw-bold text-uppercase">Total Histórico</div>
            <div class="h3 fw-bold mb-0">{{ $stats['total_history'] }}</div>
            <div class="text-muted small">Turnos procesados</div>
        </div>
    </div>
</div>

<!-- Resumen por Profesional -->
<div class="card mb-5 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-dark flex items-center gap-2">
            <i class="bi bi-people-fill text-primary"></i> Resumen de Turnos por Profesional / Médico
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="px-4 py-3">Profesional / Médico</th>
                        <th class="py-3">Especialidad</th>
                        <th class="text-center py-3">Hoy</th>
                        <th class="text-center py-3">Pendientes (Activos)</th>
                        <th class="text-center py-3">Cancelados</th>
                        <th class="text-center py-3">Total Histórico</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($professionals as $prof)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                @if($prof->avatar)
                                    <img src="{{ asset('storage/' . $prof->avatar) }}" class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover; border: 1px solid #dee2e6;">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-center font-bold" style="width: 38px; height: 38px; font-size: 14px;">
                                        {{ strtoupper(substr($prof->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark">{{ $prof->name }}</div>
                                    <div class="text-muted small">{{ $prof->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-light text-dark border border-secondary-subtle px-2.5 py-1.5">{{ $prof->specialty ?? 'General' }}</span>
                        </td>
                        <td class="text-center py-3 fw-semibold text-primary">
                            {{ $prof->today_count }}
                        </td>
                        <td class="text-center py-3 fw-semibold text-success">
                            {{ $prof->pending_count }}
                        </td>
                        <td class="text-center py-3 fw-semibold text-danger">
                            {{ $prof->cancelled_count }}
                        </td>
                        <td class="text-center py-3 fw-semibold text-info">
                            {{ $prof->total_history }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-5 text-center text-muted">
                            <i class="bi bi-person-badge text-muted fs-2 mb-2 d-block"></i>
                            No hay profesionales registrados en el sistema.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Próximos Turnos</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="px-4">Paciente</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                                <th class="text-end px-4">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nextAppointments as $app)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold">{{ $app->full_patient_name }}</div>
                                    <div class="text-muted small">{{ $app->patient_phone }}</div>
                                </td>
                                <td>{{ $app->date->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($app->time)->format('H:i') }} hs</td>
                                <td>
                                    <span class="badge rounded-pill bg-{{ $app->status === 'active' ? 'primary' : 'warning' }}">
                                        {{ $app->status }}
                                    </span>
                                </td>
                                <td class="text-end px-4">
                                    <a href="{{ route('admin.appointments.reschedule.form', $app) }}" class="btn btn-sm btn-outline-primary">
                                        Reprogramar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted">
                                    No hay turnos próximos.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Acciones Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.appointments.index') }}" class="btn btn-primary">
                        <i class="bi bi-list"></i> Ver Todos los Turnos
                    </a>
                    <a href="{{ route('admin.schedule.edit') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-gear"></i> Ajustar Agenda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
