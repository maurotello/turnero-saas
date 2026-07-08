<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelar Turno - {{ $company->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8faff; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .cancel-card { background: white; border-radius: 20px; padding: 3rem; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 500px; text-align: center; }
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
            <link rel="icon" type="image/{{ pathinfo($faviconCompany->logo, PATHINFO_EXTENSION) === 'svg' ? 'svg+xml' : 'png' }}" href="{{ asset('storage/' . $faviconCompany->logo) }}">
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
    <div class="cancel-card">
        <h2 class="fw-bold mb-4">Cancelar Turno</h2>
        
        @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif

        <p class="text-muted mb-4">
            Estás por cancelar tu turno del día 
            <strong>{{ $appointment->date->format('d/m/Y') }}</strong> a las <strong>{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }} hs</strong>.
        </p>

        @if($canCancel)
            <form action="{{ route('booking.cancel', $appointment->cancel_token) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger btn-lg w-100 rounded-pill mb-3">Sí, quiero cancelar mi turno</button>
            </form>
        @else
            <div class="alert alert-warning">
                No es posible cancelar este turno vía web porque faltan menos de 48 horas. 
                Por favor, comuníquese telefónicamente al {{ $company->phone }}.
            </div>
        @endif

        <a href="{{ route('booking.show', $company->slug) }}" class="text-decoration-none text-muted small">Volver</a>
    </div>
</body>
</html>
