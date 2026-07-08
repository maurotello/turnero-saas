<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;
        $permissions = $company->role_permissions ?? [];

        // Define available permissions and their descriptive labels
        $availablePermissions = [
            'edit_company_info' => 'Editar datos y logo de la empresa',
            'manage_schedules' => 'Configurar horarios de atención de agenda',
            'manage_blocked_days' => 'Bloquear días laborables / Feriados',
            'create_appointments' => 'Crear turnos manualmente desde el Panel',
            'cancel_appointments' => 'Cancelar turnos existentes',
        ];

        return view('admin.permissions.index', compact('permissions', 'availablePermissions'));
    }

    public function update(Request $request)
    {
        $company = auth()->user()->company;

        $request->validate([
            'permissions' => 'nullable|array',
        ]);

        // Sanitize and structure the input permission matrix
        $inputPermissions = $request->input('permissions', []);
        $sanitizedPermissions = [
            'staff' => [],
            'doctor' => [],
        ];

        $keys = [
            'edit_company_info',
            'manage_schedules',
            'manage_blocked_days',
            'create_appointments',
            'cancel_appointments',
        ];

        foreach (['staff', 'doctor'] as $role) {
            foreach ($keys as $key) {
                // Set boolean value based on form input checkboxes
                $sanitizedPermissions[$role][$key] = isset($inputPermissions[$role][$key]) && $inputPermissions[$role][$key] == '1';
            }
        }

        $company->update([
            'role_permissions' => $sanitizedPermissions,
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permisos de roles actualizados correctamente.');
    }
}
