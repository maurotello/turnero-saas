<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_settings', function (Blueprint $table) {
            $table->foreignId('appointment_type_id')->nullable()->constrained('appointment_types')->onDelete('set null');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('appointment_type_id')->nullable()->constrained('appointment_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('appointment_type_id');
        });

        Schema::table('schedule_settings', function (Blueprint $table) {
            $table->dropColumn('appointment_type_id');
        });
    }
};
