<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Exitosa - {{ $company->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8faff; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .success-card { background: white; border-radius: 20px; padding: 3rem; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 500px; text-align: center; }
        .check-icon { font-size: 4rem; color: #198754; margin-bottom: 1rem; }
    </style>
    <!-- Dynamic Favicon -->
    @php
        $faviconCompany = null;
        if (isset($company)) {
            $faviconCompany = $company;
        } elseif (auth()->check() && auth()->user()->company) {
            $faviconCompany = auth()->user()->company;
        }
    @endphp

    @if($faviconCompany)
        @if($faviconCompany->logo)
            <link rel="icon" type="image/{{ pathinfo($faviconCompany->logo, PATHINFO_EXTENSION) === 'svg' ? 'svg+xml' : 'png' }}" href="{{ Storage::url($faviconCompany->logo) }}">
        @else
            @php
                $initial = strtoupper(substr($faviconCompany->name, 0, 1));
                $primaryColor = $faviconCompany->primary_color ?? '#009BA4';
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" rx="20" fill="' . $primaryColor . '"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-family="\'Inter\', sans-serif" font-weight="bold" font-size="60" fill="#FFFFFF">' . $initial . '</text></svg>';
                $base64Svg = base64_encode($svg);
            @endphp
            <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;base64,{{ $base64Svg }}">
        @endif
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
</head>
<body>
    <div class="success-card">
        <div class="check-icon"><i class="bi bi-check-circle-fill"></i></div>
        <h2 class="fw-bold mb-3">¡Turno Confirmado!</h2>
        <p class="text-muted mb-4">
            Hola <strong>{{ $appointment->patient_first_name }}</strong>, recibimos tu reserva para el día 
            <strong>{{ $appointment->date->format('d/m/Y') }}</strong> a las <strong>{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }} hs</strong>.
        </p>
        <div class="alert alert-info py-2 small">
            Te enviamos un email con el comprobante adjunto.
        </div>
        <hr>
        <p class="small text-muted mb-4">Recordá venir 10 minutos antes.</p>
        <a href="{{ route('booking.show', $company->slug) }}" class="btn btn-outline-primary px-4 rounded-pill">Volver al inicio</a>
    </div>
</body>
</html>
