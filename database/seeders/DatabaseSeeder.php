<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ScheduleSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Company
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

        $company = Company::create([
            'name' => 'NutriSalud Viedma',
            'slug' => 'nutrisalud-viedma',
            'address' => 'Av. Caseros 123',
            'city' => 'Viedma',
            'state' => 'Río Negro',
            'country' => 'Argentina',
            'phone' => '2920-123456',
            'email' => 'contacto@nutrisalud.com',
            'professional_name' => 'Dra. María García',
            'professional_title' => 'Lic. en Nutrición',
            'specialty' => 'Nutrición Deportiva',
            'consultation_price' => 5000,
            'role_permissions' => $defaultPermissions,
        ]);

        // 2. Create default Professional
        $professional = \App\Models\Professional::create([
            'company_id' => $company->id,
            'name' => 'Dra. María García',
            'specialty' => 'Nutrición Deportiva',
            'email' => 'contacto@nutrisalud.com',
            'phone' => '2920-123456',
            'is_active' => true,
        ]);

        // 3. Create default AppointmentType
        $appType = \App\Models\AppointmentType::create([
            'company_id' => $company->id,
            'name' => 'Estándar',
            'duration' => 30,
            'price' => 0.00,
            'is_active' => true,
        ]);

        // 4. Create Admin User linked to company and professional
        User::create([
            'name' => 'Admin NutriSalud',
            'email' => 'admin@nutrisalud.com',
            'password' => Hash::make('admin123'),
            'company_id' => $company->id,
            'professional_id' => $professional->id,
            'role' => 'doctor_admin',
        ]);

        // 5. Create Default Schedule linked to the professional (Mon-Fri 08:00 to 12:00, 30 min)
        for ($i = 1; $i <= 5; $i++) {
            ScheduleSetting::create([
                'company_id' => $company->id,
                'professional_id' => $professional->id,
                'appointment_type_id' => $appType->id,
                'day_of_week' => $i,
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'slot_duration' => 30,
                'is_active' => true,
            ]);
        }
    }
}
