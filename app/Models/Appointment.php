<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'patient_id',
        'professional_id',
        'appointment_type_id',
        'date',
        'time',
        'patient_first_name',
        'patient_last_name',
        'patient_phone',
        'patient_email',
        'patient_insurance',
        'patient_dni',
        'status',
        'payment_method',
        'payment_reference',
        'source',
        'cancel_token',
        'lock_token',
        'locked_until',
        'original_date',
        'original_time',
        'rescheduled_by',
        'rescheduled_at',
        'cancelled_at',
        'cancellation_reason',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'locked_until' => 'datetime',
        'rescheduled_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function appointmentType()
    {
        return $this->belongsTo(AppointmentType::class);
    }

    public function rescheduledBy()
    {
        return $this->belongsTo(User::class, 'rescheduled_by');
    }

    public function getFullPatientNameAttribute()
    {
        return "{$this->patient_first_name} {$this->patient_last_name}";
    }
}
