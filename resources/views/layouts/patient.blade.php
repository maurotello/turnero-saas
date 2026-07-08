<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Turnos') - {{ $company->name }}</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: {{ $company->primary_color ?? '#0d6efd' }};
            --primary-hover: {{ $company->primary_color ? $company->primary_color . 'cc' : '#0b5ed7' }};
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            color: #212529;
            display: flex;
            flex-column: column;
            min-height: 100vh;
        }

        .navbar-patient {
            background-color: #ffffff;
            border-bottom: 1px solid #e9ecef;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
        }

        .text-primary-custom {
            color: var(--primary-color);
        }

        .card-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
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

    @yield('styles')
</head>
<body class="d-flex flex-column">

    <!-- Topbar Paciente -->
    <nav class="navbar navbar-expand-lg navbar-patient py-3 shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('booking.show', $company->slug) }}">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" class="rounded-circle bg-light border" style="width: 40px; height: 40px; object-fit: cover;">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: 700;">
                        {{ strtoupper(substr($company->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <span class="fw-bold text-dark fs-5">{{ $company->name }}</span>
                    @if($company->professional_name)
                        <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $company->professional_title }} {{ $company->professional_name }}</small>
                    @endif
                </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#patientNavbar" aria-controls="patientNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="patientNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-md-4">
                    <li class="nav-item">
                        <a class="nav-link fw-medium text-dark" href="{{ route('booking.show', $company->slug) }}">
                            <i class="bi bi-calendar-plus me-1 text-primary-custom"></i> Reservar Turno
                        </a>
                    </li>
                    @auth('patient')
                    <li class="nav-item">
                        <a class="nav-link fw-medium text-dark" href="{{ route('booking.dashboard', $company->slug) }}">
                            <i class="bi bi-calendar-check me-1 text-primary-custom"></i> Mis Turnos
                        </a>
                    </li>
                    @endauth
                </ul>
                
                <div class="d-flex align-items-center gap-2">
                    @auth('patient')
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2 py-2 px-3 border" type="button" id="patientMenu" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 50px;">
                                <i class="bi bi-person-circle"></i>
                                <span class="fw-medium text-dark small">{{ auth('patient')->user()->first_name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="patientMenu" style="border-radius: 12px; min-width: 200px;">
                                <div class="px-3 py-2 border-bottom">
                                    <span class="d-block text-dark fw-bold small">{{ auth('patient')->user()->full_name }}</span>
                                    <span class="d-block text-muted text-truncate" style="font-size: 0.75rem;">{{ auth('patient')->user()->email }}</span>
                                </div>
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('booking.profile', $company->slug) }}">
                                        <i class="bi bi-person text-muted"></i> Mi Perfil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('booking.dashboard', $company->slug) }}">
                                        <i class="bi bi-calendar3 text-muted"></i> Mis Turnos
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form method="POST" action="{{ route('booking.logout', $company->slug) }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                                            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('booking.login', $company->slug) }}" class="btn btn-link text-decoration-none fw-medium text-dark">Iniciar Sesión</a>
                        <a href="{{ route('booking.register', $company->slug) }}" class="btn btn-primary px-4 py-2" style="border-radius: 50px;">Registrarse</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="container my-5 flex-grow-1">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 12px;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 12px;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 12px;">
                <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer bg-white py-4 mt-auto border-top">
        <div class="container text-center">
            <span class="text-muted small">© {{ date('Y') }} {{ $company->name }}. Turnero gestionado de forma segura con Turnero SaaS.</span>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
