<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professional extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'specialty',
        'email',
        'phone',
        'avatar',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
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

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
