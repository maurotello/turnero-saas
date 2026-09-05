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
        if ($superadmin) {
            $superadmin->email = 'contacto@maurotello.com.ar';
            $superadmin->password = \Illuminate\Support\Facades\Hash::make('password123$%&');
            $superadmin->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $superadmin = \App\Models\User::where('role', 'superadmin')->first();
        if ($superadmin) {
            $superadmin->email = 'superadmin@turnero.com';
            $superadmin->password = \Illuminate\Support\Facades\Hash::make('superadmin123');
            $superadmin->save();
        }
    }
};
