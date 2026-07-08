@extends('layouts.admin')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="fw-bold mb-0">Tipos de Turnos</h2>
        <p class="text-muted">Configurá las diferentes categorías de atención, sus duraciones y precios.</p>
    </div>
    <div>
        <a href="{{ route('admin.appointment-types.create') }}" class="btn btn-primary fw-bold" style="border-radius: 8px;">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Tipo de Turno
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0 bg-white">
    <div class="card-body p-0">
        @if($appointmentTypes->isEmpty())
            <div class="p-5 text-center">
                <i class="bi bi-calendar2-range text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 fw-bold">No hay tipos de turnos registrados</h5>
                <p class="text-muted small">Registrá al menos un tipo de consulta para poder configurar la agenda y habilitar turnos.</p>
                <a href="{{ route('admin.appointment-types.create') }}" class="btn btn-sm btn-primary fw-bold mt-2">
                    Crear el primero
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="px-4 py-3">Nombre</th>
                            <th class="py-3">Duración</th>
                            <th class="py-3">Precio</th>
                            <th class="py-3">Estado</th>
                            <th class="px-4 py-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointmentTypes as $type)
                            <tr>
                                <td class="px-4 fw-bold text-dark">{{ $type->name }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border border-secondary-subtle">
                                        <i class="bi bi-clock me-1"></i> {{ $type->duration }} min
                                    </span>
                                </td>
                                <td>
                                    @if($type->price > 0)
                                        <strong class="text-success">${{ number_format($type->price, 2, ',', '.') }}</strong>
                                    @else
                                        <span class="text-muted small">Gratuito / Sin precio</span>
                                    @endif
                                </td>
                                <td>
                                    @if($type->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('admin.appointment-types.edit', $type) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;">
                                            <i class="bi bi-pencil-fill"></i> Editar
                                        </a>
                                        <form action="{{ route('admin.appointment-types.destroy', $type) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este tipo de turno?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">
                                                <i class="bi bi-trash3-fill"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
