<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Turnero Saas</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #0d6efd;
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
            background: #212529;
            color: #fff;
            border-right: 1px solid #e9ecef;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: #adb5bd;
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-left: 4px solid var(--primary-color);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column py-4">
        <div class="px-4 mb-4">
            <h4 class="m-0 fw-bold">Super Admin</h4>
            <small class="text-muted">Gestión Global</small>
        </div>

        <nav class="nav flex-column flex-grow-1">
            <a class="nav-link active" href="{{ route('superadmin.dashboard') }}">
                <i class="bi bi-grid-1x2"></i> Panel Principal
            </a>
        </nav>

        <div class="mt-auto px-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent text-danger">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
