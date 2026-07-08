<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $company;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->company = $appointment->company;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Turno — ' . $this->company->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointment-confirmation',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.appointment', [
            'appointment' => $this->appointment,
            'company' => $this->company
        ]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'Turno-' . $this->appointment->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
