@extends('layouts.admin')

@section('title', 'Detalles de Formación Académica')
@section('page-title', 'Detalles de Formación Académica')
@section('page-description', 'Información completa de la formación')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-mortarboard me-2"></i>
                    {{ $education['degree'] ?? 'Sin título' }}
                </h5>
                <div>
                    @php
                        $typeLabels = [
                            'degree' => ['text' => 'Título', 'class' => 'primary'],
                            'certification' => ['text' => 'Certificación', 'class' => 'success'],
                            'course' => ['text' => 'Curso', 'class' => 'info'],
                            'bootcamp' => ['text' => 'Bootcamp', 'class' => 'warning'],
                            'workshop' => ['text' => 'Taller', 'class' => 'secondary']
                        ];
                        $type = $education['type'] ?? 'degree';
                        $typeInfo = $typeLabels[$type] ?? $typeLabels['degree'];
                    @endphp
                    <span class="badge bg-{{ $typeInfo['class'] }}">{{ $typeInfo['text'] }}</span>
                    @if($education['is_featured'] ?? false)
                        <span class="badge bg-warning ms-1">Destacado</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary">
                            <i class="bi bi-building me-2"></i>
                            {{ $education['institution'] ?? 'Sin institución' }}
                        </h6>
                        @if(!empty($education['location']))
                            <p class="text-muted mb-3">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $education['location'] }}
                            </p>
                        @endif
                    </div>
                </div>

                @if(!empty($education['field_of_study']))
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Campo de Estudio:</strong>
                            <span class="text-muted">{{ $education['field_of_study'] }}</span>
                        </div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        @if(!empty($education['start_date']))
                            <strong>Fecha de Inicio:</strong>
                            <br>
                            <span class="text-muted">
                                {{ \Carbon\Carbon::parse($education['start_date'])->format('d/m/Y') }}
                                ({{ \Carbon\Carbon::parse($education['start_date'])->format('M Y') }})
                            </span>
                        @else
                            <span class="text-muted">Sin fecha de inicio</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($education['is_current'] ?? false)
                            <strong>Estado:</strong>
                            <br>
                            <span class="badge bg-success">En curso</span>
                        @elseif(!empty($education['end_date']))
                            <strong>Fecha de Finalización:</strong>
                            <br>
                            <span class="text-muted">
                                {{ \Carbon\Carbon::parse($education['end_date'])->format('d/m/Y') }}
                                ({{ \Carbon\Carbon::parse($education['end_date'])->format('M Y') }})
                            </span>
                        @else
                            <span class="text-muted">Sin fecha de finalización</span>
                        @endif
                    </div>
                </div>

                @if(!empty($education['grade']))
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Calificación/Nota:</strong>
                            <span class="text-muted">{{ $education['grade'] }}</span>
                        </div>
                    </div>
                @endif

                @if(!empty($education['description']))
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Descripción:</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                {!! nl2br(e($education['description'])) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    @if(!empty($education['institution_url']))
                        <div class="col-md-6 mb-2">
                            <a href="{{ $education['institution_url'] }}"
                               target="_blank"
                               class="btn btn-outline-primary w-100">
                                <i class="bi bi-link-45deg me-2"></i>
                                Visitar Institución
                            </a>
                        </div>
                    @endif
                    @if(!empty($education['certificate_url']))
                        <div class="col-md-6 mb-2">
                            <a href="{{ $education['certificate_url'] }}"
                               target="_blank"
                               class="btn btn-outline-success w-100">
                                <i class="bi bi-file-earmark-pdf me-2"></i>
                                Ver Certificado
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
                    <a href="{{ route('admin.education.edit', $education['id']) }}"
                       class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>
                        Editar Formación
                    </a>

                    <a href="{{ route('admin.education.index') }}"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Volver a la Lista
                    </a>

                    <hr>

                    <form action="{{ route('admin.education.destroy', $education['id']) }}"
                          method="POST"
                          onsubmit="return confirm('¿Estás seguro de eliminar esta formación académica? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-2"></i>
                            Eliminar Formación
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
                        <strong>ID:</strong> {{ $education['id'] ?? 'No disponible' }}
                    </div>
                    <div class="mb-2">
                        <strong>Orden de visualización:</strong> {{ $education['order_position'] ?? 0 }}
                    </div>
                    <div class="mb-2">
                        <strong>Creado:</strong>
                        {{ !empty($education['created_at']) ? \Carbon\Carbon::parse($education['created_at'])->format('d/m/Y H:i') : 'No disponible' }}
                    </div>
                    @if(!empty($education['updated_at']) && $education['updated_at'] !== $education['created_at'])
                        <div class="mb-2">
                            <strong>Última actualización:</strong>
                            {{ \Carbon\Carbon::parse($education['updated_at'])->format('d/m/Y H:i') }}
                        </div>
                    @endif
                    <div class="mb-2">
                        <strong>Estado de visualización:</strong>
                        @if($education['is_featured'] ?? false)
                            <span class="badge bg-warning">Destacado</span>
                        @else
                            <span class="badge bg-light text-dark">Normal</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($education['start_date']) && !empty($education['end_date']) && !($education['is_current'] ?? false))
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
                        $startDate = \Carbon\Carbon::parse($education['start_date']);
                        $endDate = \Carbon\Carbon::parse($education['end_date']);
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
