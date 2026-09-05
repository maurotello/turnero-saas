@extends('layouts.superadmin')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Vista General del Sistema</h2>
    </div>

    <div class="row g-4 mb-4">
        <!-- Tarjeta Total Empresas -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-subtitle text-muted mb-0">Clínicas Registradas</h6>
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                    <h2 class="card-title mb-0">{{ $totalCompanies }}</h2>
                </div>
            </div>
        </div>

        <!-- Tarjeta Total Usuarios -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-subtitle text-muted mb-0">Usuarios Totales</h6>
                        <div class="bg-success bg-opacity-10 text-success rounded p-2">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                    <h2 class="card-title mb-0">{{ $totalUsers }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Listado de Clínicas / Sanatorios</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Clínica</th>
                            <th>Ubicación</th>
                            <th>Profesional Principal</th>
                            <th>Usuarios Asignados</th>
                            <th>Fecha de Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $company)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $company->name }}</div>
                                    <div class="text-muted small">URL: /booking/{{ $company->slug }}</div>
                                </td>
                                <td>
                                    {{ $company->city }}, {{ $company->state }}
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $company->professional_name }}</div>
                                    <div class="text-muted small">{{ $company->specialty }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary rounded-pill">{{ $company->users_count }} usuarios</span>
                                </td>
                                <td>
                                    {{ $company->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No hay empresas registradas aún.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
