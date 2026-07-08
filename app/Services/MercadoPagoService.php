<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    /**
     * Determina si se debe cobrar por el turno.
     */
    public function shouldCharge(Company $company): bool
    {
        return !empty($company->mp_access_token) && (float)$company->consultation_price > 0;
    }

    /**
     * Crea una preferencia de pago en Mercado Pago y retorna el link de pago (init_point), o null si falla.
     */
    public function createPreferenceForAppointment(Appointment $appointment): ?string
    {
        $company = $appointment->company;

        if (!$this->shouldCharge($company)) {
            return null;
        }

        try {
            $payer = [
                'name' => $appointment->patient_first_name,
                'surname' => $appointment->patient_last_name,
            ];

            if (!empty($appointment->patient_email)) {
                $payer['email'] = $appointment->patient_email;
            }

            if (!empty($appointment->patient_phone)) {
                $payer['phone'] = [
                    'number' => $appointment->patient_phone,
                ];
            }

            $response = Http::withToken($company->mp_access_token)
                ->post('https://api.mercadopago.com/checkout/preferences', [
                    'items' => [
                        [
                            'title' => "Turno: " . ($company->professional_title ?? 'Consulta') . " - " . $company->professional_name,
                            'quantity' => 1,
                            'unit_price' => (float) $company->consultation_price,
                            'currency_id' => 'ARS',
                        ]
                    ],
                    'payer' => $payer,
                    'back_urls' => [
                        'success' => route('booking.payment-success', [$company->slug, 'appointment_id' => $appointment->id]),
                        'failure' => route('booking.show', $company->slug) . '?payment_status=failure',
                        'pending' => route('booking.show', $company->slug) . '?payment_status=pending',
                    ],
                    'auto_return' => 'approved',
                    'external_reference' => (string) $appointment->id,
                    'notification_url' => secure_url(route('booking.mp-webhook', $company->slug)),
                ]);

            if ($response->successful()) {
                $preference = $response->json();
                return $preference['init_point'] ?? null;
            } else {
                Log::error('MP Preference Creation Failed', ['response' => $response->body()]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('MP Preference Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
