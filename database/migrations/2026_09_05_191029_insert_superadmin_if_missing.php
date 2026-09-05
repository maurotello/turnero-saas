<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $superadmin = \App\Models\User::where('role', 'superadmin')->first();
        if (!$superadmin) {
            \App\Models\User::create([
                'name' => 'Super Admin',
                'email' => 'contacto@maurotello.com.ar',
                'password' => \Illuminate\Support\Facades\Hash::make('password123$%&'),
                'role' => 'superadmin',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\User::where('email', 'contacto@maurotello.com.ar')->delete();
    }
};
