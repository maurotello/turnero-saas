<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Restablece el hash de password del super_admin usando el query builder
     * (no Eloquent), para evitar que el cast 'password' => 'hashed' del modelo
     * User vuelva a hashear un valor que ya podría estar mal.
     */
    public function up(): void
    {
        $email = 'contacto@maurotello.com.ar';

        $exists = DB::table('users')->where('email', $email)->exists();

        if ($exists) {
            DB::table('users')
                ->where('email', $email)
                ->update([
                    'password' => Hash::make('password123$%&'),
                    'role' => 'super_admin',
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * No hay nada sensato que revertir en un reseteo de password.
     */
    public function down(): void
    {
        //
    }
};
