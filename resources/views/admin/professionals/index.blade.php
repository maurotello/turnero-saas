@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Profesionales</h2>
        <p class="text-muted mb-0">Gestioná los profesionales de tu consultorio o empresa y sus calendarios de turnos.</p>
    </div>
    <div>
        <a href="{{ route('admin.professionals.create') }}" class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2">
            <i class="bi bi-person-plus-fill"></i> Agregar Profesional
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        @if($professionals->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3 fw-medium">No hay profesionales registrados.</h5>
                <p class="text-muted small">Registrá al menos un profesional para que tu turnero público empiece a recibir reservas.</p>
                <a href="{{ route('admin.professionals.create') }}" class="btn btn-primary btn-sm mt-2">Agregar mi primer profesional</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted uppercase font-monospace" style="font-size: 0.8rem;">
                        <tr>
                            <th class="py-3 px-4">Profesional</th>
                            <th class="py-3">Especialidad</th>
                            <th class="py-3">Contacto</th>
                            <th class="py-3">Estado</th>
                            <th class="py-3 text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($professionals as $professional)
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($professional->avatar)
                                            <img src="{{ Storage::url($professional->avatar) }}" class="rounded-circle border" style="width: 44px; height: 44px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center border" style="width: 44px; height: 44px; font-weight: 600; font-size: 0.95rem;">
                                                {{ strtoupper(substr($professional->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $professional->name }}</span>
                                            <small class="text-muted">ID: #{{ $professional->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-light text-primary border border-primary-subtle px-3 py-1.5" style="border-radius: 50px;">
                                        {{ $professional->specialty ?? 'General' }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    @if($professional->email)
                                        <div class="text-muted small"><i class="bi bi-envelope me-1"></i>{{ $professional->email }}</div>
                                    @endif
                                    @if($professional->phone)
                                        <div class="text-muted small mt-1"><i class="bi bi-telephone me-1"></i>{{ $professional->phone }}</div>
                                    @endif
                                    @if(!$professional->email && !$professional->phone)
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($professional->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5" style="border-radius: 50px;">
                                            <i class="bi bi-check-circle-fill me-1"></i> Activo
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1.5" style="border-radius: 50px;">
                                            <i class="bi bi-slash-circle me-1"></i> Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 text-end px-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.professionals.edit', $professional->id) }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 px-3" style="border-radius: 50px;">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <form action="{{ route('admin.professionals.destroy', $professional->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este profesional? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1 px-3" style="border-radius: 50px;">
                                                <i class="bi bi-trash"></i> Eliminar
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
