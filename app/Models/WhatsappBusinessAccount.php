<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappBusinessAccount extends Model
{
    protected $fillable = [
        'company_id',
        'phone_number_id',
        'waba_id',
        'display_phone_number',
        'access_token',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
