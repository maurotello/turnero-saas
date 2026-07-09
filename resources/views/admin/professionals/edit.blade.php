@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.professionals.index') }}" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i>Volver al listado</a>
    <h2 class="fw-bold mb-0 mt-2">Editar Profesional</h2>
    <p class="text-muted">Actualizá los datos de perfil y el estado de disponibilidad del profesional.</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Información del Profesional</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.professionals.update', $professional->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label small fw-bold text-muted">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $professional->name) }}" required placeholder="Ej: Dr. Alejandro Ramos">
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="specialty" class="form-label small fw-bold text-muted">Especialidad</label>
                            <input type="text" name="specialty" id="specialty" class="form-control" value="{{ old('specialty', $professional->specialty) }}" placeholder="Ej: Odontólogo General, Pediatra...">
                            @error('specialty')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-bold text-muted">Email (Opcional)</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $professional->email) }}" placeholder="ejemplo@correo.com">
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label small fw-bold text-muted">Teléfono de Contacto (Opcional)</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $professional->phone) }}" placeholder="Ej: 2920123456">
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="avatar" class="form-label small fw-bold text-muted">Foto de Perfil (Opcional)</label>
                            <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">
                            <p class="text-muted small mt-1.5" style="font-size: 0.75rem;">Formatos recomendados: JPG, PNG o WEBP. Máximo 2MB.</p>
                            @error('avatar')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            @if($professional->avatar)
                                <div class="mt-3">
                                    <label class="form-label small d-block text-muted">Avatar Actual:</label>
                                    <img src="{{ Storage::url($professional->avatar) }}" class="rounded-circle img-thumbnail bg-light" style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                            @endif
                        </div>

                        <div class="col-12 mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $professional->is_active ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold text-muted" for="is_active">
                                    Profesional Activo (Habilita la reserva de turnos en su agenda)
                                </label>
                            </div>
                        </div>

                        <div class="col-12 border-top pt-3 mt-4 text-end">
                            <a href="{{ route('admin.professionals.index') }}" class="btn btn-secondary px-4 me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
