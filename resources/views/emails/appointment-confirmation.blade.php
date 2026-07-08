<x-mail::message>
# Hola {{ $appointment->patient_first_name }},

Tu turno en **{{ $company->name }}** ha sido confirmado con éxito.

**Detalles del turno:**
- **Fecha:** {{ $appointment->date->format('d/m/Y') }}
- **Hora:** {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }} hs
- **Profesional:** {{ $company->professional_name }}

Adjunto encontrarás el comprobante en PDF.

<x-mail::panel>
**Regla de Cancelación:** Si necesitas cancelar o reprogramar, debes hacerlo con al menos 48 horas de anticipación.
</x-mail::panel>

Si deseas cancelar tu turno, puedes hacerlo presionando el siguiente botón:

<x-mail::button :url="route('booking.cancel.form', $appointment->cancel_token)">
Cancelar mi turno
</x-mail::button>

Gracias,<br>
El equipo de {{ $company->name }}
</x-mail::message>
