<?php

namespace App\Http\Controllers;

use App\Models\WhatsappBusinessAccount;
use App\Models\WhatsappMessageLog;
use App\Jobs\ProcessIncomingWhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * GET: Verificación del Token del Webhook (Meta)
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === config('services.whatsapp.verify_token')) {
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            }
        }

        return response('Unauthorized', 403);
    }

    /**
     * POST: Recepción de eventos desde Meta
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        
        // 1. Validar la firma X-Hub-Signature-256
        if (!$this->validateSignature($request)) {
            Log::channel('whatsapp')->warning('Webhook Signature Invalid');
            return response('Invalid signature', 401);
        }

        // 2. Extraer el entry principal
        $entry = $payload['entry'][0] ?? null;
        if (!$entry) {
            return response('No entry found', 200);
        }

        $changes = $entry['changes'][0] ?? null;
        $value = $changes['value'] ?? null;
        
        // Si no es un evento de mensajes, ignoramos pero respondemos 200
        if (!$value || !isset($value['messages'][0])) {
            return response('No messages in payload', 200);
        }

        $message = $value['messages'][0];
        $messageId = $message['id'];
        $phoneNumberId = $value['metadata']['phone_number_id'] ?? null;

        // 3. Buscar la cuenta WABA asociada (Multi-tenant)
        $waba = WhatsappBusinessAccount::where('phone_number_id', $phoneNumberId)
            ->where('is_active', true)
            ->first();

        if (!$waba) {
            Log::channel('whatsapp')->warning("WABA Account not found or inactive for Phone Number ID: {$phoneNumberId}");
            return response('WABA Account not registered', 200); // 200 para evitar reintentos de Meta
        }

        // 4. Garantizar Idempotencia: Verificar si el mensaje ya está registrado
        $exists = WhatsappMessageLog::where('whatsapp_message_id', $messageId)->exists();
        if ($exists) {
            return response('Duplicate message ignored', 200);
        }

        // 5. Despachar el procesamiento asincrónico a la cola (Job)
        ProcessIncomingWhatsAppMessage::dispatch($waba, $message, $value);

        return response('Event Received', 200);
    }

    /**
     * Valida que la firma X-Hub-Signature-256 coincida usando el APP_SECRET.
     */
    protected function validateSignature(Request $request): bool
    {
        $signatureHeader = $request->header('X-Hub-Signature-256');
        if (!$signatureHeader) {
            return false;
        }

        $signatureParts = explode('sha256=', $signatureHeader);
        $signature = $signatureParts[1] ?? null;
        if (!$signature) {
            return false;
        }

        $appSecret = config('services.whatsapp.app_secret');
        $calculatedSignature = hash_hmac('sha256', $request->getContent(), $appSecret);

        return hash_equals($signature, $calculatedSignature);
    }
}
