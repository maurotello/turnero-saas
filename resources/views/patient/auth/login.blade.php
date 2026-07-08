@extends('layouts.patient')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card card-custom p-4 bg-white">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-1 text-dark">Iniciar Sesión</h3>
                    <p class="text-muted small">Accedé a tu portal para gestionar turnos y perfiles.</p>
                </div>

                <form method="POST" action="{{ route('booking.login.submit', array_merge(['slug' => $company->slug], request()->only(['professional_id', 'appointment_type_id', 'date', 'time']))) }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold text-muted">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input id="email" class="form-control border-start-0 bg-light" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="ejemplo@correo.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label small fw-bold text-muted">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                            <input id="password" class="form-control border-start-0 bg-light" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                            <label for="remember_me" class="form-check-label small text-muted">Recordarme</label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary btn-lg py-2.5 fs-6" style="border-radius: 10px;">
                            Ingresar
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4 pt-3 border-top">
                    <p class="text-muted small mb-0">¿No tenés cuenta aún? 
                        <a href="{{ route('booking.register', array_merge(['slug' => $company->slug], request()->only(['professional_id', 'appointment_type_id', 'date', 'time']))) }}" class="text-primary-custom fw-semibold text-decoration-none">Registrate acá</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
