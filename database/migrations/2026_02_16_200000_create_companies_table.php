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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            
            // Professional data
            $table->string('professional_name')->nullable();
            $table->string('professional_title')->nullable();
            $table->string('specialty')->nullable();
            $table->string('license_number')->nullable();
            $table->decimal('consultation_price', 10, 2)->nullable();
            
            // UI/UX config
            $table->string('timezone')->default('America/Argentina/Buenos_Aires');
            $table->string('primary_color', 7)->default('#0d6efd');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
