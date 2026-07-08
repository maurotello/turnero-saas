@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-0">Mi Perfil</h2>
    <p class="text-muted">Administrá tu información de cuenta y configurá tu seguridad.</p>
</div>

<div class="row">
    <!-- Información de Perfil -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-person-gear text-primary me-2"></i>Información de la Cuenta</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">Actualizá tu nombre de usuario y dirección de correo electrónico.</p>
                
                <form method="post" action="{{ route('admin.profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-bold text-muted">Nombre</label>
                        <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autocomplete="name">
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold text-muted">Email</label>
                        <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
                        
                        @if (session('status') === 'profile-updated')
                            <span class="text-success small"><i class="bi bi-check-circle-fill me-1"></i>Guardado con éxito.</span>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cambio de Contraseña -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-lock text-primary me-2"></i>Cambiar Contraseña</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">Asegurate de usar una contraseña larga y aleatoria para mantener tu cuenta segura.</p>

                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="mb-3">
                        <label for="update_password_current_password" class="form-label small fw-bold text-muted">Contraseña Actual</label>
                        <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" required>
                        @error('current_password', 'updatePassword')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="update_password_password" class="form-label small fw-bold text-muted">Nueva Contraseña</label>
                        <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" required>
                        @error('password', 'updatePassword')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="update_password_password_confirmation" class="form-label small fw-bold text-muted">Confirmar Nueva Contraseña</label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" required>
                        @error('password_confirmation', 'updatePassword')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4">Actualizar Contraseña</button>
                        
                        @if (session('status') === 'password-updated')
                            <span class="text-success small"><i class="bi bi-check-circle-fill me-1"></i>Contraseña actualizada.</span>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Eliminar Cuenta -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0 border-start border-danger border-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Zona de Peligro</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Una vez que elimines tu cuenta, todos los datos asociados se borrarán de forma permanente e irreversible.</p>
                
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                    Eliminar Cuenta Administrador
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminación -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="{{ route('admin.profile.destroy') }}">
                @csrf
                @method('delete')
                
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold text-danger" id="confirmDeleteModalLabel">¿Estás seguro de que quieres eliminar tu cuenta?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body py-0">
                    <p class="text-muted small">Por favor, ingresá tu contraseña para confirmar que deseás eliminar esta cuenta de forma permanente.</p>
                    
                    <div class="mb-3">
                        <input id="password" name="password" type="password" class="form-control" placeholder="Contraseña de administrador" required>
                        @error('password', 'userDeletion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Definitivamente</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
