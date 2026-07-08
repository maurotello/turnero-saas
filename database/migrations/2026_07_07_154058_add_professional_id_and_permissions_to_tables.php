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
        // 1. Modificar users (cambiar role a string para soportar múltiples roles dinámicos en Sqlite)
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->change();
            $table->foreignId('professional_id')->nullable()->constrained('professionals')->onDelete('set null');
        });

        // 2. Modificar appointments
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('professional_id')->nullable()->constrained('professionals')->onDelete('set null');
        });

        // 3. Modificar schedule_settings
        Schema::table('schedule_settings', function (Blueprint $table) {
            $table->foreignId('professional_id')->nullable()->constrained('professionals')->onDelete('cascade');
        });

        // 4. Modificar blocked_days
        Schema::table('blocked_days', function (Blueprint $table) {
            $table->foreignId('professional_id')->nullable()->constrained('professionals')->onDelete('cascade');
        });

        // 5. Modificar companies
        Schema::table('companies', function (Blueprint $table) {
            $table->json('role_permissions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('role_permissions');
        });

        Schema::table('blocked_days', function (Blueprint $table) {
            $table->dropForeign(['professional_id']);
            $table->dropColumn('professional_id');
        });

        Schema::table('schedule_settings', function (Blueprint $table) {
            $table->dropForeign(['professional_id']);
            $table->dropColumn('professional_id');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['professional_id']);
            $table->dropColumn('professional_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['professional_id']);
            $table->dropColumn('professional_id');
        });
    }
};
