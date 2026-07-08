<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            // Ampliamos el ENUM para soportar pending_payment de forma nativa en MySQL
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('active', 'cancelled', 'rescheduled', 'pending_payment') NOT NULL DEFAULT 'active'");
        }
    }

    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            // Regresamos al ENUM original
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('active', 'cancelled', 'rescheduled') NOT NULL DEFAULT 'active'");
        }
    }
};
