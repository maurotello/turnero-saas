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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('time');
            
            // Patient info
            $table->string('patient_first_name');
            $table->string('patient_last_name');
            $table->string('patient_phone');
            $table->string('patient_email');
            $table->string('patient_insurance')->nullable();
            
            // Status and tracking
            $table->enum('status', ['active', 'cancelled', 'rescheduled'])->default('active');
            $table->string('cancel_token', 64)->unique();
            
            // Temporary locking for completing reservation
            $table->string('lock_token', 64)->nullable();
            $table->timestamp('locked_until')->nullable();
            
            // Rescheduling history
            $table->date('original_date')->nullable();
            $table->time('original_time')->nullable();
            $table->foreignId('rescheduled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rescheduled_at')->nullable();
            
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Optimization
            $table->index(['company_id', 'date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
