@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.appointment-types.index') }}" class="text-decoration-none small fw-semibold text-muted">
        <i class="bi bi-arrow-left"></i> Volver al listado
    </a>
    <h2 class="fw-bold mb-0 mt-2">Nuevo Tipo de Turno</h2>
    <p class="text-muted">Creá un nuevo servicio o tipo de consulta para asociar a la agenda.</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 bg-white">
            <form action="{{ route('admin.appointment-types.store') }}" method="POST">
                @csrf
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold small text-dark">Nombre del Servicio / Consulta</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ej: Primera Consulta, Limpieza Completa, Extracción" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duration" class="form-label fw-bold small text-dark">Duración (minutos)</label>
                                <div class="input-group">
                                    <input type="number" name="duration" id="duration" class="form-control @error('duration') is-invalid @enderror" value="{{ old('duration', 30) }}" min="5" max="1440" required>
                                    <span class="input-group-text">minutos</span>
                                </div>
                                <div class="form-text small text-muted">Tiempo estimado que ocupará el turno en la agenda.</div>
                                @error('duration')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label fw-bold small text-dark">Precio sugerido (opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price" id="price" step="0.01" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="0.00" min="0">
                                </div>
                                <div class="form-text small text-muted">Dejar vacío si es una consulta gratuita o sin valor fijo.</div>
                                @error('price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small text-dark" for="is_active">Habilitar Tipo de Turno</label>
                        </div>
                        <div class="form-text small text-muted">Si está inactivo, los pacientes no podrán reservar este tipo de turno y no aparecerá en las agendas nuevas.</div>
                    </div>
                </div>
                <div class="card-footer bg-white p-3 text-end border-0">
                    <button type="submit" class="btn btn-primary px-5 fw-bold" style="border-radius: 8px;">Crear Tipo de Turno</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
