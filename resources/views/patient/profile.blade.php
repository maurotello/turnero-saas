@extends('layouts.patient')

@section('title', 'Mi Perfil')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <h3 class="fw-bold mb-1">Mi Perfil</h3>
        <p class="text-muted small">Gestioná tus datos personales, dirección de contacto y seguridad de la cuenta.</p>
    </div>

    <!-- Editar Datos Personales -->
    <div class="col-lg-7 mb-4">
        <div class="card card-custom p-4 bg-white h-100">
            <div class="card-body p-0">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-person-lines-fill text-primary-custom me-2"></i>Datos Personales y Contacto</h5>
                <hr class="mb-4">

                <form method="POST" action="{{ route('booking.profile.update', $company->slug) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label small fw-bold text-muted">Nombre</label>
                            <input id="first_name" class="form-control" type="text" name="first_name" value="{{ old('first_name', $patient->first_name) }}" required>
                            @error('first_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="last_name" class="form-label small fw-bold text-muted">Apellido</label>
                            <input id="last_name" class="form-control" type="text" name="last_name" value="{{ old('last_name', $patient->last_name) }}" required>
                            @error('last_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="dni" class="form-label small fw-bold text-muted">DNI</label>
                            <input id="dni" class="form-control" type="text" name="dni" value="{{ old('dni', $patient->dni) }}" required>
                            @error('dni')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="insurance" class="form-label small fw-bold text-muted">Obra Social / Prepaga</label>
                            <input id="insurance" class="form-control" type="text" name="insurance" value="{{ old('insurance', $patient->insurance) }}" required>
                            @error('insurance')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-bold text-muted">Email</label>
                            <input id="email" class="form-control" type="email" name="email" value="{{ old('email', $patient->email) }}" required>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label small fw-bold text-muted">Teléfono</label>
                            <input id="phone" class="form-control" type="tel" name="phone" value="{{ old('phone', $patient->phone) }}" required>
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label for="address" class="form-label small fw-bold text-muted">Dirección</label>
                            <input id="address" class="form-control" type="text" name="address" value="{{ old('address', $patient->address) }}">
                            @error('address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="zip_code" class="form-label small fw-bold text-muted">Código Postal</label>
                            <input id="zip_code" class="form-control" type="text" name="zip_code" value="{{ old('zip_code', $patient->zip_code) }}">
                            @error('zip_code')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="city" class="form-label small fw-bold text-muted">Ciudad</label>
                            <input id="city" class="form-control" type="text" name="city" value="{{ old('city', $patient->city) }}">
                            @error('city')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="state" class="form-label small fw-bold text-muted">Provincia</label>
                            <input id="state" class="form-control" type="text" name="state" value="{{ old('state', $patient->state) }}">
                            @error('state')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 8px;">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cambio de Contraseña -->
    <div class="col-lg-5 mb-4">
        <div class="card card-custom p-4 bg-white h-100">
            <div class="card-body p-0">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-shield-lock-fill text-primary-custom me-2"></i>Seguridad</h5>
                <hr class="mb-4">

                <form method="POST" action="{{ route('booking.profile.password', $company->slug) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label small fw-bold text-muted">Contraseña Actual</label>
                        <input id="current_password" class="form-control bg-light" type="password" name="current_password" required placeholder="••••••••">
                        @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label small fw-bold text-muted">Nueva Contraseña</label>
                        <input id="password" class="form-control bg-light" type="password" name="password" required placeholder="Mínimo 8 caracteres">
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label small fw-bold text-muted">Confirmar Nueva Contraseña</label>
                        <input id="password_confirmation" class="form-control bg-light" type="password" name="password_confirmation" required placeholder="••••••••">
                        @error('password_confirmation')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 8px;">
                            Actualizar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
