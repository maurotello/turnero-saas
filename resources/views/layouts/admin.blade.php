<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - {{ auth()->user()->company->name }}</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: {{ auth()->user()->company->primary_color ?? '#0d6efd' }};
            --sidebar-width: 260px;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #ffffff;
            border-right: 1px solid #e9ecef;
            z-index: 1000;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 0 2rem 2rem 2rem;
        }

        .nav-link {
            color: #6c757d;
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
            background: #f0f7ff;
        }

        .nav-link.active {
            border-right: 4px solid var(--primary-color);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .navbar-top {
            position: sticky;
            top: 0;
            z-index: 990;
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
            margin-left: -2rem;
            margin-right: -2rem;
            padding: 1rem 2rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
                padding: 0 1rem 1rem 1rem;
            }
            .navbar-top {
                margin-left: -1rem;
                margin-right: -1rem;
                padding: 1rem;
            }
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
<body>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column p-3">
        <div class="mb-4 px-3 d-flex align-items-center gap-2">
            <div id="sidebarLogoContainer">
                @if(auth()->user()->company->logo)
                    <img src="{{ asset('storage/' . auth()->user()->company->logo) }}" id="sidebarLogo" class="rounded-circle bg-light border" style="width: 32px; height: 32px; object-fit: cover;">
                @endif
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-primary" style="font-size: 1.05rem;">{{ auth()->user()->company->name }}</h5>
                <small class="text-muted" style="font-size: 0.75rem;">Turnero SaaS</small>
            </div>
        </div>

        <nav class="nav flex-column gap-1">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.appointments.index') }}" class="nav-link {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Turnos
            </a>
            
            @if(auth()->user()->isAdmin())
            <div class="mt-4 px-3 mb-2"><small class="text-uppercase fw-bold text-muted small">Configuración</small></div>
            <a href="{{ route('admin.professionals.index') }}" class="nav-link {{ request()->routeIs('admin.professionals.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Profesionales
            </a>
            <a href="{{ route('admin.appointment-types.index') }}" class="nav-link {{ request()->routeIs('admin.appointment-types.*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-range"></i> Tipos de Turnos
            </a>
            <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i> Permisos de Roles
            </a>
            <a href="{{ route('admin.schedule.edit') }}" class="nav-link {{ request()->routeIs('admin.schedule.*') ? 'active' : '' }}">
                <i class="bi bi-clock"></i> Horarios Agenda
            </a>
            <a href="{{ route('admin.blocked-days.index') }}" class="nav-link {{ request()->routeIs('admin.blocked-days.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-x"></i> Días Bloqueados
            </a>
            <a href="{{ route('admin.company.edit') }}" class="nav-link {{ request()->routeIs('admin.company.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Datos Empresa
            </a>
            @endif

            <div class="mt-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link text-danger border-0 bg-transparent w-100 text-start">
                        <i class="bi bi-box-arrow-right"></i> Salir
                    </button>
                </form>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navigation Bar -->
        <header class="navbar-top d-flex justify-content-between align-items-center shadow-sm">
            <div>
                <h6 class="fw-bold mb-0 text-muted small text-uppercase tracking-wider d-none d-sm-block">Panel Administrativo</h6>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-link text-decoration-none dropdown-toggle d-flex align-items-center gap-2 p-0 border-0" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <div id="topbarUserImageContainer">
                        @if(auth()->user()->company->logo)
                            <img src="{{ asset('storage/' . auth()->user()->company->logo) }}" id="topbarUserLogo" class="rounded-circle bg-light border" style="width: 36px; height: 36px; object-fit: cover;">
                        @else
                            <div id="topbarUserInitials" class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-weight: 600; font-size: 0.85rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <span class="fw-semibold text-dark small d-none d-sm-inline">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userMenu" style="border-radius: 12px; min-width: 220px; font-size: 0.9rem;">
                    <div class="px-3 py-2 border-bottom">
                        <span class="d-block text-dark fw-bold small">{{ auth()->user()->name }}</span>
                        <span class="d-block text-muted text-truncate" style="font-size: 0.75rem;">{{ auth()->user()->email }}</span>
                    </div>
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.profile.edit') }}">
                            <i class="bi bi-person text-muted"></i> Mi Perfil
                        </a>
                    </li>
                    @if(auth()->user()->isAdmin())
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.company.edit') }}">
                            <i class="bi bi-building text-muted"></i> Datos Empresa
                        </a>
                    </li>
                    @endif
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-right"></i> Salir
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        @if(auth()->user()->company && auth()->user()->company->professionals()->where('is_active', true)->count() === 0)
            <div class="alert alert-warning rounded-0 border-0 m-0 py-3 text-center shadow-sm" style="background-color: #fff3cd; color: #664d03; position: sticky; top: 70px; z-index: 980;">
                <div class="container-fluid">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Atención:</strong> No tenés profesionales activos configurados. El turnero público está deshabilitado hasta que agregues al menos un profesional.
                    <a href="{{ route('admin.professionals.create') }}" class="alert-link text-decoration-underline ms-2">Crear Profesional ahora</a>
                </div>
            </div>
        @elseif(auth()->user()->company && auth()->user()->company->appointmentTypes()->where('is_active', true)->count() === 0)
            <div class="alert alert-warning rounded-0 border-0 m-0 py-3 text-center shadow-sm" style="background-color: #fff3cd; color: #664d03; position: sticky; top: 70px; z-index: 980;">
                <div class="container-fluid">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Atención:</strong> No tenés tipos de turnos activos configurados. El turnero público está deshabilitado hasta que crees al menos un tipo de turno.
                    <a href="{{ route('admin.appointment-types.create') }}" class="alert-link text-decoration-underline ms-2">Crear Tipo de Turno ahora</a>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
