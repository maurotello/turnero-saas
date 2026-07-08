<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $expression = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite'
                ? "CASE WHEN status IN ('active', 'rescheduled', 'pending_payment') THEN company_id || '-' || date || '-' || time ELSE NULL END"
                : "CASE WHEN status IN ('active', 'rescheduled', 'pending_payment') THEN CONCAT(company_id, '-', date, '-', time) ELSE NULL END";

            $table->string('active_slot_unique')
                ->nullable()
                ->storedAs($expression);
            
            $table->unique('active_slot_unique', 'uq_active_slots');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique('uq_active_slots');
            $table->dropColumn('active_slot_unique');
        });
    }
};
