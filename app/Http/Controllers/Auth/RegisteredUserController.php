<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $company = \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            // 1. Create the Company with default permissions configuration
            $defaultPermissions = [
                'staff' => [
                    'edit_company_info' => false,
                    'manage_schedules' => true,
                    'manage_blocked_days' => true,
                    'create_appointments' => true,
                    'cancel_appointments' => true,
                ],
                'doctor' => [
                    'edit_company_info' => false,
                    'manage_schedules' => true,
                    'manage_blocked_days' => true,
                    'create_appointments' => true,
                    'cancel_appointments' => true,
                ]
            ];

            $company = \App\Models\Company::create([
                'name' => $request->company_name,
                'slug' => \Illuminate\Support\Str::slug($request->company_name),
                'email' => $request->email,
                'role_permissions' => $defaultPermissions,
                'cancellation_hours_limit' => 24, // Valor por defecto
            ]);

            // 2. Create the first Professional record (linked to the creator)
            $professional = \App\Models\Professional::create([
                'company_id' => $company->id,
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => true,
            ]);

            // 3. Create the default "Estándar" AppointmentType
            \App\Models\AppointmentType::create([
                'company_id' => $company->id,
                'name' => 'Estándar',
                'duration' => 15,
                'price' => 0.00,
                'is_active' => true,
            ]);

            // 3. Create the administrative User linked to both the Company and the Professional
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company_id' => $company->id,
                'professional_id' => $professional->id,
                'role' => 'doctor_admin', // Creador es Profesional + Admin por defecto
            ]);

            event(new Registered($user));

            return $company;
        });

        // Iniciar sesión con el usuario recién registrado
        Auth::login($company->users()->first());

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }
}
