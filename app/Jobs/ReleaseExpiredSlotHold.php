<?php

namespace App\Jobs;

use App\Models\Appointment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReleaseExpiredSlotHold implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $appointment;

    /**
     * Create a new job instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Obtener el estado fresco desde la base de datos
        $appointment = $this->appointment->fresh();

        if ($appointment && $appointment->status === 'pending_payment') {
            Log::channel('whatsapp')->info('Hold expired. Releasing pending payment slot.', [
                'appointment_id' => $appointment->id,
                'patient' => "{$appointment->patient_first_name} {$appointment->patient_last_name}",
                'date' => $appointment->date->format('Y-m-d'),
                'time' => $appointment->time,
            ]);

            // Se elimina el registro para liberar físicamente el slot en CalendarService
            $appointment->delete();
        }
    }
}
