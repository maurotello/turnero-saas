<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role',
        'professional_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->role === 'doctor_admin';
    }

    public function isDoctor()
    {
        return $this->role === 'doctor' || $this->role === 'doctor_admin';
    }

    public function isStaff()
    {
        return in_array($this->role, ['admin', 'doctor_admin', 'doctor', 'staff']);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
