@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard
            </h1>
            <p class="text-muted">Resumen general del CMS Portfolio</p>
        </div>
        <div class="text-muted">
            <i class="bi bi-clock me-1"></i>Última actualización: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <!-- Proyectos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Proyectos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['projects']['total'] }}
                            </div>
                            <small class="text-muted">
                                {{ $stats['projects']['featured'] }} destacados
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-folder fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Perfil -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Perfil Completado
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['profile']['percentage'] }}%
                            </div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $stats['profile']['percentage'] }}%"></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-circle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formación -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Formación
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['education']['total'] }}
                            </div>
                            <small class="text-muted">
                                {{ $stats['education']['current'] }} en curso
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-mortarboard fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Experiencia -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Experiencia
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['experience']['total'] }}
                            </div>
                            <small class="text-muted">
                                {{ $stats['experience']['current'] }} actual
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-briefcase fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Accesos rápidos -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-lightning me-2"></i>Accesos Rápidos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.projects.create') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="bi bi-plus-circle fs-2 mb-2"></i>
                                <span>Nuevo Proyecto</span>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.education.create') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="bi bi-mortarboard fs-2 mb-2"></i>
                                <span>Añadir Formación</span>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.experience.create') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="bi bi-briefcase fs-2 mb-2"></i>
                                <span>Nueva Experiencia</span>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.profile.edit') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="bi bi-person-gear fs-2 mb-2"></i>
                                <span>Editar Perfil</span>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.social.create') }}" class="btn btn-outline-purple w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="bi bi-share fs-2 mb-2"></i>
                                <span>Enlace Social</span>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="bi bi-gear fs-2 mb-2"></i>
                                <span>Configuración</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estado del perfil -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-person-check me-2"></i>Estado del Perfil
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="progress mb-2" style="height: 20px;">
                            <div class="progress-bar bg-gradient-success" role="progressbar"
                                 style="width: {{ $stats['profile']['percentage'] }}%">
                                {{ $stats['profile']['percentage'] }}%
                            </div>
                        </div>
                        <small class="text-muted">Completado</small>
                    </div>

                    @if(!empty($stats['profile']['missing']))
                        <div class="alert alert-warning alert-sm">
                            <strong>Campos pendientes:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($stats['profile']['missing'] as $field)
                                    <li>{{ ucfirst(str_replace('_', ' ', $field)) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-success alert-sm">
                            <i class="bi bi-check-circle me-2"></i>
                            ¡Perfil completado!
                        </div>
                    @endif

                    <div class="mt-3">
                        <small class="text-muted">
                            <strong>Requeridos:</strong> {{ $stats['profile']['completed_required'] }}/{{ $stats['profile']['total_required'] }}<br>
                            <strong>Opcionales:</strong> {{ $stats['profile']['completed_optional'] }}/{{ $stats['profile']['total_optional'] }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad reciente -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-clock-history me-2"></i>Actividad Reciente
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($stats['recent_activity']) > 0)
                        <ul class="list-unstyled mb-0">
                            @foreach($stats['recent_activity'] as $activity)
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span>{{ $activity['action'] }}</span>
                                    <small class="text-muted">{{ $activity['time'] }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">No hay actividad reciente que mostrar.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-info { border-left: 4px solid #36b9cc !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }
    .btn-outline-purple {
        color: #6f42c1;
        border-color: #6f42c1;
    }
    .btn-outline-purple:hover {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: white;
    }
    .alert-sm { font-size: 0.875rem; }
</style>
@endsection
