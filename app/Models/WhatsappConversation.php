<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappConversation extends Model
{
    protected $fillable = [
        'company_id',
        'patient_phone',
        'state',
        'context_json',
        'last_message_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'context_json' => 'array',
            'last_message_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function logs()
    {
        return $this->hasMany(WhatsappMessageLog::class);
    }
}
