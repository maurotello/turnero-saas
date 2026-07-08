@extends('layouts.patient')

@section('title', 'Registrarse')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-custom p-4 bg-white">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-1 text-dark">Crear Cuenta de Paciente</h3>
                    <p class="text-muted small">Registrate para poder agendar turnos y consultar tu historial.</p>
                </div>

                <form method="POST" action="{{ route('booking.register.submit', array_merge(['slug' => $company->slug], request()->only(['professional_id', 'appointment_type_id', 'date', 'time']))) }}">
                    @csrf

                    <div class="row g-3">
                        <!-- Datos Personales -->
                        <div class="col-12"><h6 class="fw-bold text-primary-custom mb-1 border-bottom pb-2">1. Datos Personales</h6></div>
                        
                        <div class="col-md-6">
                            <label for="first_name" class="form-label small fw-bold text-muted">Nombre <span class="text-danger">*</span></label>
                            <input id="first_name" class="form-control bg-light" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus placeholder="Ej: María">
                            @error('first_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="last_name" class="form-label small fw-bold text-muted">Apellido <span class="text-danger">*</span></label>
                            <input id="last_name" class="form-control bg-light" type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Ej: García">
                            @error('last_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="dni" class="form-label small fw-bold text-muted">DNI <span class="text-danger">*</span></label>
                            <input id="dni" class="form-control bg-light" type="text" name="dni" value="{{ old('dni') }}" required placeholder="Sin puntos ni espacios">
                            @error('dni')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="insurance" class="form-label small fw-bold text-muted">Obra Social / Prepaga <span class="text-danger">*</span></label>
                            <input id="insurance" class="form-control bg-light" type="text" name="insurance" value="{{ old('insurance') }}" required placeholder="Ej: OSDE 310, Particular...">
                            @error('insurance')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Datos de Contacto -->
                        <div class="col-12 mt-4"><h6 class="fw-bold text-primary-custom mb-1 border-bottom pb-2">2. Contacto y Dirección</h6></div>

                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-bold text-muted">Correo Electrónico <span class="text-danger">*</span></label>
                            <input id="email" class="form-control bg-light" type="email" name="email" value="{{ old('email') }}" required placeholder="ejemplo@correo.com">
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label small fw-bold text-muted">Teléfono de Contacto <span class="text-danger">*</span></label>
                            <input id="phone" class="form-control bg-light" type="tel" name="phone" value="{{ old('phone') }}" required placeholder="Ej: 2920123456">
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label for="address" class="form-label small fw-bold text-muted">Dirección</label>
                            <input id="address" class="form-control bg-light" type="text" name="address" value="{{ old('address') }}" placeholder="Calle y número">
                            @error('address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="zip_code" class="form-label small fw-bold text-muted">Código Postal</label>
                            <input id="zip_code" class="form-control bg-light" type="text" name="zip_code" value="{{ old('zip_code') }}" placeholder="Ej: 8500">
                            @error('zip_code')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="city" class="form-label small fw-bold text-muted">Ciudad</label>
                            <input id="city" class="form-control bg-light" type="text" name="city" value="{{ old('city') }}" placeholder="Ej: Viedma">
                            @error('city')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="state" class="form-label small fw-bold text-muted">Provincia</label>
                            <input id="state" class="form-control bg-light" type="text" name="state" value="{{ old('state') }}" placeholder="Ej: Río Negro">
                            @error('state')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Seguridad -->
                        <div class="col-12 mt-4"><h6 class="fw-bold text-primary-custom mb-1 border-bottom pb-2">3. Contraseña de Acceso</h6></div>

                        <div class="col-md-6">
                            <label for="password" class="form-label small fw-bold text-muted">Contraseña <span class="text-danger">*</span></label>
                            <input id="password" class="form-control bg-light" type="password" name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres">
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label small fw-bold text-muted">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <input id="password_confirmation" class="form-control bg-light" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                            @error('password_confirmation')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg py-2.5 fs-6" style="border-radius: 10px;">
                            Registrarme
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4 pt-3 border-top">
                    <p class="text-muted small mb-0">¿Ya tenés una cuenta? 
                        <a href="{{ route('booking.login', array_merge(['slug' => $company->slug], request()->only(['professional_id', 'appointment_type_id', 'date', 'time']))) }}" class="text-primary-custom fw-semibold text-decoration-none">Iniciá sesión acá</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
