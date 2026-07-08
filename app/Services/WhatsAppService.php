<?php

namespace App\Services;

use App\Models\WhatsappBusinessAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envia un mensaje de texto simple.
     */
    public function sendTextMessage(WhatsappBusinessAccount $waba, string $to, string $text)
    {
        return $this->callGraphApi($waba, 'messages', [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $text
            ]
        ]);
    }

    /**
     * Envia Reply Buttons interactivas (ej: confirmación Sí/No).
     */
    public function sendReplyButtons(WhatsappBusinessAccount $waba, string $to, string $text, array $buttons)
    {
        $formattedButtons = [];
        foreach ($buttons as $id => $title) {
            $formattedButtons[] = [
                'type' => 'reply',
                'reply' => [
                    'id' => $id,
                    'title' => mb_substr($title, 0, 20) // Límite de Meta
                ]
            ];
        }

        return $this->callGraphApi($waba, 'messages', [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $text
                ],
                'action' => [
                    'buttons' => $formattedButtons
                ]
            ]
        ]);
    }

    /**
     * Envia un List Message interactivo (ej: selección de días u horarios).
     */
    public function sendListMessage(WhatsappBusinessAccount $waba, string $to, string $text, string $buttonText, string $title, array $options)
    {
        $rows = [];
        foreach ($options as $id => $label) {
            $row = [
                'id' => $id,
            ];
            
            if (is_array($label)) {
                $row['title'] = mb_substr($label['title'] ?? '', 0, 24);
                if (!empty($label['description'])) {
                    $row['description'] = mb_substr($label['description'], 0, 72);
                }
            } else {
                $row['title'] = mb_substr($label, 0, 24);
            }
            
            $rows[] = $row;
        }

        return $this->callGraphApi($waba, 'messages', [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => [
                    'text' => $text
                ],
                'action' => [
                    'button' => mb_substr($buttonText, 0, 20),
                    'sections' => [
                        [
                            'title' => mb_substr($title, 0, 24),
                            'rows' => $rows
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Normaliza los números telefónicos argentinos para envíos salientes.
     * Remueve el "9" tras el código de país "54" si tiene al menos 13 dígitos de longitud total.
     */
    public function normalizePhoneForSending(string $phone): string
    {
        if (str_starts_with($phone, '549') && strlen($phone) >= 13) {
            return '54' . substr($phone, 3);
        }

        return $phone;
    }

    /**
     * Método central de llamada HTTP a la API de Meta Graph.
     */
    protected function callGraphApi(WhatsappBusinessAccount $waba, string $endpoint, array $payload)
    {
        // Normalizar el destinatario únicamente para el payload saliente
        if (isset($payload['to'])) {
            $payload['to'] = $this->normalizePhoneForSending($payload['to']);
        }

        try {
            $url = "https://graph.facebook.com/v20.0/{$waba->phone_number_id}/{$endpoint}";
            
            $response = Http::withToken($waba->access_token)
                ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::channel('whatsapp')->info('WhatsApp Message Outbound Successful', [
                    'to' => $payload['to'],
                    'message_id' => $data['messages'][0]['id'] ?? null
                ]);

                return $data;
            }

            Log::channel('whatsapp')->error('WhatsApp API Error Response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload
            ]);

            return null;
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('WhatsApp Connection Exception', [
                'message' => $e->getMessage(),
                'payload' => $payload
            ]);
            return null;
        }
    }
}
