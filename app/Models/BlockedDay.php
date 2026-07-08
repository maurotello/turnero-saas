<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedDay extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id', 'professional_id', 'date', 'reason'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}
