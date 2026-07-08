<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSetting extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id', 'professional_id', 'day_of_week', 'start_time', 'end_time', 
        'slot_duration', 'is_active', 'appointment_type_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function appointmentType()
    {
        return $this->belongsTo(AppointmentType::class);
    }
}
