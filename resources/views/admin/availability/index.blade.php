@extends('layouts.admin')

@section('title', 'Gestión de Disponibilidad')
@section('page-title', 'Gestión de Disponibilidad')
@section('page-description', 'Controla tu estado de disponibilidad para el portafolio')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock me-2"></i>
                        Estado Actual de Disponibilidad
                    </h5>
                    <a href="{{ route('admin.availability.edit') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>
                        Editar
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($availability)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Estado Actual</h6>
                                <div class="d-flex align-items-center">
                                    @switch($availability['status'])
                                        @case('available')
                                            <span class="badge bg-success me-2">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Disponible
                                            </span>
                                            @break
                                        @case('busy')
                                            <span class="badge bg-warning me-2">
                                                <i class="bi bi-clock me-1"></i>
                                                Ocupado
                                            </span>
                                            @break
                                        @case('unavailable')
                                            <span class="badge bg-danger me-2">
                                                <i class="bi bi-x-circle me-1"></i>
                                                No Disponible
                                            </span>
                                            @break
                                    @endswitch
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Tiempo de Respuesta</h6>
                                <p class="mb-0">
                                    <i class="bi bi-stopwatch me-1"></i>
                                    {{ $availability['response_time'] ?? 'No especificado' }}
                                </p>
                            </div>

                            @if($availability['custom_message'])
                                <div class="mb-4">
                                    <h6 class="text-muted mb-2">Mensaje Personalizado</h6>
                                    <p class="mb-0">{{ $availability['custom_message'] }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            @if($availability['show_calendar_link'] ?? false)
                                <div class="mb-4">
                                    <h6 class="text-muted mb-2">Enlace de Calendario</h6>
                                    <p class="mb-0">
                                        <i class="bi bi-check-circle text-success me-1"></i>
                                        Mostrar enlace habilitado
                                    </p>
                                    @if($availability['calendar_url'])
                                        <small class="text-muted">
                                            <a href="{{ $availability['calendar_url'] }}" target="_blank">
                                                {{ $availability['calendar_url'] }}
                                            </a>
                                        </small>
                                    @endif
                                </div>
                            @endif

                            @if(isset($availability['availability_details']) && is_array($availability['availability_details']))
                                <div class="mb-4">
                                    <h6 class="text-muted mb-2">Detalles Adicionales</h6>
                                    @if(isset($availability['availability_details']['preferred_contact']))
                                        <p class="mb-1">
                                            <strong>Contacto preferido:</strong>
                                            {{ $availability['availability_details']['preferred_contact'] }}
                                        </p>
                                    @endif
                                    @if(isset($availability['availability_details']['timezone']))
                                        <p class="mb-1">
                                            <strong>Zona horaria:</strong>
                                            {{ $availability['availability_details']['timezone'] }}
                                        </p>
                                    @endif
                                    @if(isset($availability['availability_details']['working_hours']))
                                        <p class="mb-1">
                                            <strong>Horario:</strong>
                                            {{ $availability['availability_details']['working_hours'] }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Última Actualización</h6>
                                <p class="mb-0">
                                    <i class="bi bi-calendar me-1"></i>
                                    {{ \Carbon\Carbon::parse($availability['last_updated'] ?? $availability['updated_at'])->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-clock text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">No hay información de disponibilidad</h5>
                        <p class="text-muted mb-4">Configura tu estado de disponibilidad para el portafolio.</p>
                        <a href="{{ route('admin.availability.edit') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Configurar Disponibilidad
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Cambio Rápido de Estado
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Cambia rápidamente tu estado sin editar otros campos:</p>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success" onclick="quickStatusUpdate('available')">
                        <i class="bi bi-check-circle me-2"></i>
                        Marcar como Disponible
                    </button>
                    <button type="button" class="btn btn-warning" onclick="quickStatusUpdate('busy')">
                        <i class="bi bi-clock me-2"></i>
                        Marcar como Ocupado
                    </button>
                    <button type="button" class="btn btn-danger" onclick="quickStatusUpdate('unavailable')">
                        <i class="bi bi-x-circle me-2"></i>
                        Marcar como No Disponible
                    </button>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Información
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        <strong>Disponible:</strong> Aceptando nuevos proyectos
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-clock text-warning me-2"></i>
                        <strong>Ocupado:</strong> Con proyectos actuales, respuesta más lenta
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-x-circle text-danger me-2"></i>
                        <strong>No Disponible:</strong> No aceptando proyectos nuevos
                    </li>
                </ul>
            </div>
        </div>
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

<script>
function quickStatusUpdate(status) {
    if (confirm('¿Cambiar el estado de disponibilidad?')) {
        fetch('{{ route("admin.availability.quick-update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Error desconocido'));
            }
        })
        .catch(error => {
            alert('Error de conexión');
        });
    }
}
</script>
@endsection
