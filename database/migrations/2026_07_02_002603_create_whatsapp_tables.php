<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de mapeo Multi-tenant (Meta phone_number_id -> Company)
        Schema::create('whatsapp_business_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('phone_number_id')->unique();
            $table->string('waba_id');
            $table->string('display_phone_number');
            $table->text('access_token'); // Se guardará encriptado
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabla de estados de la conversación (sesión del bot)
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('patient_phone');
            $table->string('state')->default('inicio');
            $table->json('context_json')->nullable();
            $table->timestamp('last_message_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'patient_phone']);
        });

        // Tabla de logs para auditoría e idempotencia de mensajes
        Schema::create('whatsapp_message_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('whatsapp_conversation_id')->nullable()->constrained('whatsapp_conversations')->onDelete('set null');
            $table->string('whatsapp_message_id')->unique();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->string('message_type');
            $table->json('payload');
            $table->timestamps();
        });

        // Alteración de la tabla appointments para integrar datos extra
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('patient_dni')->nullable()->after('patient_insurance');
            $table->string('payment_method')->nullable()->after('status');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->string('source')->default('web')->after('payment_reference');
            
            $table->index('patient_phone');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['patient_phone']);
            $table->dropColumn(['patient_dni', 'payment_method', 'payment_reference', 'source']);
        });

        Schema::dropIfExists('whatsapp_message_logs');
        Schema::dropIfExists('whatsapp_conversations');
        Schema::dropIfExists('whatsapp_business_accounts');
    }
};
