<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('active_slot_unique')
                ->nullable();
            
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
