@extends('layouts.admin')

@section('title', 'Experiencia Laboral')
@section('page-title', 'Gestión de Experiencia Laboral')
@section('page-description', 'Administra tu historial profesional y laboral')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="bi bi-briefcase me-2"></i>
            Mi Experiencia Laboral
        </h4>
        <small class="text-muted">Total: {{ count($experiences ?? []) }} registros</small>
    </div>
    <a href="{{ route('admin.experience.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>
        Nueva Experiencia
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if(!empty($experiences) && count($experiences) > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Empresa</th>
                            <th>Cargo</th>
                            <th>Tipo</th>
                            <th>Periodo</th>
                            <th style="width: 100px;">Estado</th>
                            <th style="width: 120px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($experiences as $experience)
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    @if(!empty($experience['company_logo_url']))
                                        <img src="{{ $experience['company_logo_url'] }}"
                                             alt="Logo"
                                             class="rounded me-3"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary rounded d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-building text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $experience['company'] ?? 'Sin empresa' }}</strong>
                                        @if(!empty($experience['location']))
                                            <br><small class="text-muted">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $experience['location'] }}
                                                @if(!empty($experience['location_type']))
                                                    <span class="badge bg-light text-dark ms-1">
                                                        {{ ucfirst(str_replace('-', ' ', $experience['location_type'])) }}
                                                    </span>
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div>
                                    <strong>{{ $experience['position'] ?? 'Sin cargo' }}</strong>
                                    @if(!empty($experience['department']))
                                        <br><small class="text-muted">{{ $experience['department'] }}</small>
                                    @endif
                                </div>
                            </td>
                            <td class="align-middle">
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
                                <span class="badge bg-{{ $typeInfo['class'] }}">
                                    {{ $typeInfo['text'] }}
                                </span>
                            </td>
                            <td class="align-middle">
                                @if(!empty($experience['start_date']))
                                    @php
                                        $startDate = \Carbon\Carbon::parse($experience['start_date']);
                                        $endDate = $experience['end_date'] ? \Carbon\Carbon::parse($experience['end_date']) : null;
                                    @endphp
                                    <div>
                                        <small class="text-muted">Inicio:</small> {{ $startDate->format('M Y') }}
                                        <br>
                                        @if($experience['is_current'] ?? false)
                                            <span class="badge bg-success">Actual</span>
                                        @elseif($endDate)
                                            <small class="text-muted">Fin:</small> {{ $endDate->format('M Y') }}
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">Sin fecha</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($experience['is_featured'] ?? false)
                                    <span class="badge bg-warning">Destacado</span>
                                @else
                                    <span class="badge bg-light text-dark">Normal</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.experience.show', $experience['id']) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.experience.edit', $experience['id']) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.experience.destroy', $experience['id']) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta experiencia laboral?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-briefcase text-muted mb-3" style="font-size: 3rem;"></i>
                <h5 class="text-muted">No hay experiencia laboral registrada</h5>
                <p class="text-muted mb-4">Comienza agregando tu historial profesional.</p>
                <a href="{{ route('admin.experience.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Agregar Primera Experiencia
                </a>
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
