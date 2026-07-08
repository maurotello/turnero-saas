<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante de Turno</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 20px; }
        .details-box { background: #f9f9f9; padding: 25px; border-radius: 10px; }
        .label { font-weight: bold; color: #555; }
        .value { font-size: 18px; margin-bottom: 10px; }
        .professional-box { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company->name }}</h1>
        <p>{{ $company->address }} — {{ $company->city }}</p>
    </div>

    <div class="professional-box">
        <p><span class="label">Profesional:</span><br>
        <span class="value">{{ $company->professional_name }} ({{ $company->specialty }})</span></p>
    </div>

    <div class="details-box">
        <h3>Detalles del Turno</h3>
        <p><span class="label">Paciente:</span><br>
        <span class="value">{{ $appointment->full_patient_name }}</span></p>

        <p><span class="label">Fecha:</span><br>
        <span class="value">{{ $appointment->date->format('d/m/Y') }}</span></p>

        <p><span class="label">Hora:</span><br>
        <span class="value">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }} hs</span></p>
    </div>

    <div class="footer">
        <p>Gracias por confiar en nosotros.<br>
        Si necesita cancelar, recuerde hacerlo con al menos 48 horas de anticipación.</p>
        <p>{{ $company->phone }} | {{ $company->email }}</p>
    </div>
</body>
</html>
