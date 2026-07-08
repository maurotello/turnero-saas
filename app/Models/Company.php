<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::deleting(function ($company) {
            $company->appointments()->delete();
        });
    }

    protected $fillable = [
        'name', 'slug', 'logo', 'address', 'city', 'state', 'country', 
        'phone', 'email', 'website', 'professional_name', 'professional_title', 
        'specialty', 'license_number', 'consultation_price', 'timezone', 'primary_color',
        'mp_public_key', 'mp_access_token', 'cancellation_hours_limit', 'same_patient_rebooking_hours',
        'role_permissions'
    ];

    protected $casts = [
        'role_permissions' => 'array',
    ];

    public function professionals()
    {
        return $this->hasMany(Professional::class);
    }

    public function appointmentTypes()
    {
        return $this->hasMany(AppointmentType::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function scheduleSettings()
    {
        return $this->hasMany(ScheduleSetting::class);
    }

    public function blockedDays()
    {
        return $this->hasMany(BlockedDay::class);
    }

    /**
     * Verifica si un rol específico tiene un permiso en esta empresa.
     */
    public function hasPermission(string $role, string $permission): bool
    {
        if ($role === 'admin' || $role === 'doctor_admin') {
            return true;
        }

        $permissions = $this->role_permissions;
        if (is_array($permissions) && isset($permissions[$role][$permission])) {
            return (bool) $permissions[$role][$permission];
        }

        return false;
    }
}
