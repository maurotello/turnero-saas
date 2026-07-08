@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-0">Permisos de Roles</h2>
    <p class="text-muted">Configurá dinámicamente qué acciones puede realizar cada rol de tu consultorio en el panel administrativo.</p>
</div>

<div class="row">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-check text-primary me-2"></i>Matriz de Permisos por Rol</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.permissions.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3 px-3" style="width: 50%;">Acción / Permiso</th>
                                    <th class="py-3 text-center" style="width: 25%;">Secretaria / Asistente (<code class="text-secondary">staff</code>)</th>
                                    <th class="py-3 text-center" style="width: 25%;">Profesional / Médico (<code class="text-secondary">doctor</code>)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availablePermissions as $key => $label)
                                    <tr>
                                        <td class="py-3 px-3">
                                            <span class="fw-semibold text-dark d-block">{{ $label }}</span>
                                            <small class="text-muted">Clave: <code>{{ $key }}</code></small>
                                        </td>
                                        
                                        <!-- Permiso para Secretaria (staff) -->
                                        <td class="py-3 text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input type="hidden" name="permissions[staff][{{ $key }}]" value="0">
                                                <input class="form-check-input" type="checkbox" name="permissions[staff][{{ $key }}]" value="1" 
                                                    {{ (isset($permissions['staff'][$key]) && $permissions['staff'][$key]) ? 'checked' : '' }}
                                                    style="cursor: pointer;">
                                            </div>
                                        </td>
                                        
                                        <!-- Permiso para Médico (doctor) -->
                                        <td class="py-3 text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input type="hidden" name="permissions[doctor][{{ $key }}]" value="0">
                                                <input class="form-check-input" type="checkbox" name="permissions[doctor][{{ $key }}]" value="1" 
                                                    {{ (isset($permissions['doctor'][$key]) && $permissions['doctor'][$key]) ? 'checked' : '' }}
                                                    style="cursor: pointer;">
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-light border p-3 mt-4 text-muted small" style="border-radius: 10px;">
                        <i class="bi bi-info-circle me-1 text-primary"></i> <strong>Nota:</strong> Los roles <code>admin</code> (Administrador general) y <code>doctor_admin</code> (Médico/Dueño del sistema) tienen siempre habilitados todos los permisos de forma nativa para evitar que el sistema quede bloqueado.
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            Guardar Permisos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
