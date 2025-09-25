<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Portfolio - @yield('title', 'Admin')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        /* Estilos personalizados para el scroll de la sidebar */
        .sidebar .position-sticky {
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.3) transparent;
        }

        .sidebar .position-sticky::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar .position-sticky::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar .position-sticky::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }

        .sidebar .position-sticky::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .main-content {
            margin-left: 250px;
            padding: 0;
        }
        .top-bar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        .card {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-radius: 10px;
        }
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 8px;
        }
        .alert {
            border: none;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Sidebar -->
    <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse" id="sidebarMenu">
        <div class="position-sticky pt-3" style="height: 100vh; overflow-y: auto;">
            <div class="text-center py-3 border-bottom border-light border-opacity-25">
                <h5 class="text-white mb-0">
                    <i class="bi bi-briefcase me-2"></i>
                    CMS Portfolio
                </h5>
            </div>

            <ul class="nav flex-column mt-3">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.projects.index') ? 'active' : '' }}"
                       href="{{ route('admin.projects.index') }}">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard
                    </a>
                </li>

                <!-- Contenido -->
                <li class="nav-item mt-3">
                    <h6 class="text-white-50 small px-3 mb-2">CONTENIDO</h6>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}"
                       href="{{ route('admin.projects.index') }}">
                        <i class="bi bi-folder me-2"></i>
                        Proyectos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.education.*') ? 'active' : '' }}"
                       href="{{ route('admin.education.index') }}">
                        <i class="bi bi-mortarboard me-2"></i>
                        Formación Académica
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.experience.*') ? 'active' : '' }}"
                       href="{{ route('admin.experience.index') }}">
                        <i class="bi bi-briefcase me-2"></i>
                        Experiencia Laboral
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}"
                       href="{{ route('admin.certificates.index') }}">
                        <i class="bi bi-award me-2"></i>
                        Certificados
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.cv.*') ? 'active' : '' }}"
                       href="{{ route('admin.cv.index') }}">
                        <i class="bi bi-file-earmark-pdf me-2"></i>
                        Mi CV
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.availability.*') ? 'active' : '' }}"
                       href="{{ route('admin.availability.index') }}">
                        <i class="bi bi-clock me-2"></i>
                        Disponibilidad
                    </a>
                </li>

                <!-- Perfil -->
                <li class="nav-item mt-3">
                    <h6 class="text-white-50 small px-3 mb-2">PERFIL</h6>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
                       href="{{ route('admin.profile.index') }}">
                        <i class="bi bi-person me-2"></i>
                        Información Personal
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.social.*') ? 'active' : '' }}"
                       href="{{ route('admin.social.index') }}">
                        <i class="bi bi-share me-2"></i>
                        Enlaces Sociales
                    </a>
                </li>

                <!-- Sistema -->
                <li class="nav-item mt-3">
                    <h6 class="text-white-50 small px-3 mb-2">SISTEMA</h6>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                       href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people me-2"></i>
                        Usuarios del CMS
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                       href="{{ route('admin.settings.index') }}">
                        <i class="bi bi-gear me-2"></i>
                        Configuración
                    </a>
                </li>

                <!-- Cerrar Sesión -->
                <li class="nav-item mt-4 pt-3 border-top border-light border-opacity-25">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link text-start w-100 border-0 p-2">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar p-3 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
                    <small class="text-muted">@yield('page-description', 'Gestiona tu portafolio')</small>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @php
                        $admin = \App\Http\Controllers\Auth\LoginController::getAuthenticatedAdmin();
                    @endphp

                    @if($admin)
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success me-2">
                                <i class="bi bi-person-check me-1"></i>
                                {{ ucfirst($admin['role'] ?? 'admin') }}
                            </span>
                            <small class="text-muted">{{ $admin['name'] ?? $admin['email'] ?? 'Admin' }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="container-fluid px-4">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
