@extends('layouts.admin')

@section('title', 'Detalles de Experiencia Laboral')
@section('page-title', 'Detalles de Experiencia Laboral')
@section('page-description', 'Información completa de la experiencia profesional')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    @if(!empty($experience['company_logo_url']))
                        <img src="{{ $experience['company_logo_url'] }}"
                             alt="Logo"
                             class="rounded me-3"
                             style="width: 50px; height: 50px; object-fit: cover;">
                    @else
                        <div class="bg-primary rounded d-flex align-items-center justify-content-center me-3"
                             style="width: 50px; height: 50px;">
                            <i class="bi bi-building text-white fs-5"></i>
                        </div>
                    @endif
                    <div>
                        <h5 class="mb-0">{{ $experience['position'] ?? 'Sin cargo' }}</h5>
                        <small class="text-muted">{{ $experience['company'] ?? 'Sin empresa' }}</small>
                    </div>
                </div>
                <div>
                    @php
                        $employmentLabels = [
                            'full-time' => ['text' => 'Tiempo Completo', 'class' => 'primary'],
                            'part-time' => ['text' => 'Tiempo Parcial', 'class' => 'info'],
                            'contract' => ['text' => 'Contrato', 'class' => 'warning'],
                            'freelance' => ['text' => 'Freelance', 'class' => 'success'],
                            'internship' => ['text' => 'Prácticas', 'class' => 'secondary'],
                            'volunteer' => ['text' => 'Voluntario', 'class' => 'dark']
                        ];
                        $type = $experience['employment_type'] ?? 'full-time';
                        $typeInfo = $employmentLabels[$type] ?? $employmentLabels['full-time'];
                    @endphp
                    <span class="badge bg-{{ $typeInfo['class'] }}">{{ $typeInfo['text'] }}</span>
                    @if($experience['is_featured'] ?? false)
                        <span class="badge bg-warning ms-1">Destacado</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        @if(!empty($experience['department']))
                            <strong>Departamento:</strong>
                            <br>
                            <span class="text-muted">{{ $experience['department'] }}</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if(!empty($experience['location']))
                            <strong>Ubicación:</strong>
                            <br>
                            <span class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $experience['location'] }}
                                @if(!empty($experience['location_type']))
                                    <span class="badge bg-light text-dark ms-1">
                                        {{ ucfirst(str_replace('-', ' ', $experience['location_type'])) }}
                                    </span>
                                @endif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        @if(!empty($experience['start_date']))
                            <strong>Fecha de Inicio:</strong>
                            <br>
                            <span class="text-muted">
                                {{ \Carbon\Carbon::parse($experience['start_date'])->format('d/m/Y') }}
                                ({{ \Carbon\Carbon::parse($experience['start_date'])->format('M Y') }})
                            </span>
                        @else
                            <span class="text-muted">Sin fecha de inicio</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($experience['is_current'] ?? false)
                            <strong>Estado:</strong>
                            <br>
                            <span class="badge bg-success">Trabajo Actual</span>
                        @elseif(!empty($experience['end_date']))
                            <strong>Fecha de Finalización:</strong>
                            <br>
                            <span class="text-muted">
                                {{ \Carbon\Carbon::parse($experience['end_date'])->format('d/m/Y') }}
                                ({{ \Carbon\Carbon::parse($experience['end_date'])->format('M Y') }})
                            </span>
                        @else
                            <span class="text-muted">Sin fecha de finalización</span>
                        @endif
                    </div>
                </div>

                @if(!empty($experience['description']))
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <strong>Descripción:</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                {!! nl2br(e($experience['description'])) !!}
                            </div>
                        </div>
                    </div>
                @endif

                @if(!empty($experience['responsibilities']))
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <strong>
                                <i class="bi bi-list-check me-2"></i>
                                Responsabilidades Principales:
                            </strong>
                            @php
                                $responsibilities = is_array($experience['responsibilities']) ?
                                    $experience['responsibilities'] :
                                    json_decode($experience['responsibilities'], true);
                            @endphp
                            @if($responsibilities && count($responsibilities) > 0)
                                <ul class="mt-2">
                                    @foreach($responsibilities as $responsibility)
                                        @if(!empty(trim($responsibility)))
                                            <li class="mb-1">{{ $responsibility }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                @if(!empty($experience['achievements']))
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <strong>
                                <i class="bi bi-trophy me-2"></i>
                                Logros y Resultados:
                            </strong>
                            @php
                                $achievements = is_array($experience['achievements']) ?
                                    $experience['achievements'] :
                                    json_decode($experience['achievements'], true);
                            @endphp
                            @if($achievements && count($achievements) > 0)
                                <ul class="mt-2 text-success">
                                    @foreach($achievements as $achievement)
                                        @if(!empty(trim($achievement)))
                                            <li class="mb-1">{{ $achievement }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                @if(!empty($experience['technologies']))
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <strong>
                                <i class="bi bi-tools me-2"></i>
                                Tecnologías y Herramientas:
                            </strong>
                            @php
                                $technologies = is_array($experience['technologies']) ?
                                    $experience['technologies'] :
                                    json_decode($experience['technologies'], true);
                            @endphp
                            @if($technologies && count($technologies) > 0)
                                <div class="mt-2">
                                    @foreach($technologies as $technology)
                                        @if(!empty(trim($technology)))
                                            <span class="badge bg-primary me-1 mb-1">{{ $technology }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="row">
                    @if(!empty($experience['company_url']))
                        <div class="col-md-6 mb-2">
                            <a href="{{ $experience['company_url'] }}"
                               target="_blank"
                               class="btn btn-outline-primary w-100">
                                <i class="bi bi-link-45deg me-2"></i>
                                Visitar Empresa
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Acciones -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Acciones
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.experience.edit', $experience['id']) }}"
                       class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>
                        Editar Experiencia
                    </a>

                    <a href="{{ route('admin.experience.index') }}"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Volver a la Lista
                    </a>

                    <hr>

                    <form action="{{ route('admin.experience.destroy', $experience['id']) }}"
                          method="POST"
                          onsubmit="return confirm('¿Estás seguro de eliminar esta experiencia laboral? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-2"></i>
                            Eliminar Experiencia
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Información del Sistema -->
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Información del Sistema
                </h6>
            </div>
            <div class="card-body">
                <div class="small text-muted">
                    <div class="mb-2">
                        <strong>ID:</strong> {{ $experience['id'] ?? 'No disponible' }}
                    </div>
                    <div class="mb-2">
                        <strong>Orden de visualización:</strong> {{ $experience['order_position'] ?? 0 }}
                    </div>
                    <div class="mb-2">
                        <strong>Creado:</strong>
                        {{ !empty($experience['created_at']) ? \Carbon\Carbon::parse($experience['created_at'])->format('d/m/Y H:i') : 'No disponible' }}
                    </div>
                    @if(!empty($experience['updated_at']) && $experience['updated_at'] !== $experience['created_at'])
                        <div class="mb-2">
                            <strong>Última actualización:</strong>
                            {{ \Carbon\Carbon::parse($experience['updated_at'])->format('d/m/Y H:i') }}
                        </div>
                    @endif
                    <div class="mb-2">
                        <strong>Estado de visualización:</strong>
                        @if($experience['is_featured'] ?? false)
                            <span class="badge bg-warning">Destacado</span>
                        @else
                            <span class="badge bg-light text-dark">Normal</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($experience['start_date']) && !empty($experience['end_date']) && !($experience['is_current'] ?? false))
            <!-- Duración -->
            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-clock me-2"></i>
                        Duración
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $startDate = \Carbon\Carbon::parse($experience['start_date']);
                        $endDate = \Carbon\Carbon::parse($experience['end_date']);
                        $duration = $startDate->diff($endDate);
                        $years = $duration->y;
                        $months = $duration->m;
                    @endphp
                    <div class="text-center">
                        <div class="h4 text-primary">
                            @if($years > 0)
                                {{ $years }} año{{ $years > 1 ? 's' : '' }}
                                @if($months > 0)
                                    y {{ $months }} mes{{ $months > 1 ? 'es' : '' }}
                                @endif
                            @elseif($months > 0)
                                {{ $months }} mes{{ $months > 1 ? 'es' : '' }}
                            @else
                                Menos de 1 mes
                            @endif
                        </div>
                        <small class="text-muted">Duración total</small>
                    </div>
                </div>
            </div>
        @endif

        @if($experience['is_current'] ?? false)
            <!-- Tiempo trabajando actualmente -->
            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Tiempo en el Cargo
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $startDate = \Carbon\Carbon::parse($experience['start_date']);
                        $now = \Carbon\Carbon::now();
                        $duration = $startDate->diff($now);
                        $years = $duration->y;
                        $months = $duration->m;
                    @endphp
                    <div class="text-center">
                        <div class="h4 text-success">
                            @if($years > 0)
                                {{ $years }} año{{ $years > 1 ? 's' : '' }}
                                @if($months > 0)
                                    y {{ $months }} mes{{ $months > 1 ? 'es' : '' }}
                                @endif
                            @elseif($months > 0)
                                {{ $months }} mes{{ $months > 1 ? 'es' : '' }}
                            @else
                                Menos de 1 mes
                            @endif
                        </div>
                        <small class="text-muted">Tiempo trabajando</small>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@endsection
