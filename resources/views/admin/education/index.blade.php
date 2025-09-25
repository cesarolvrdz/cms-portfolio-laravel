@extends('layouts.admin')

@section('title', 'Formación Académica')
@section('page-title', 'Gestión de Formación Académica')
@section('page-description', 'Administra tu educación, certificaciones y cursos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="bi bi-mortarboard me-2"></i>
            Mi Formación Académica
        </h4>
        <small class="text-muted">Total: {{ count($education ?? []) }} registros</small>
    </div>
    <a href="{{ route('admin.education.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>
        Nueva Formación
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if(!empty($education) && count($education) > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Institución</th>
                            <th>Título/Certificación</th>
                            <th>Tipo</th>
                            <th>Periodo</th>
                            <th style="width: 100px;">Estado</th>
                            <th style="width: 120px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($education as $item)
                        <tr>
                            <td class="align-middle">
                                <div>
                                    <strong>{{ $item['institution'] ?? 'Sin institución' }}</strong>
                                    @if(!empty($item['location']))
                                        <br><small class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $item['location'] }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td class="align-middle">
                                <div>
                                    <strong>{{ $item['degree'] ?? 'Sin título' }}</strong>
                                    @if(!empty($item['field_of_study']))
                                        <br><small class="text-muted">{{ $item['field_of_study'] }}</small>
                                    @endif
                                </div>
                            </td>
                            <td class="align-middle">
                                @php
                                    $typeLabels = [
                                        'degree' => ['text' => 'Título', 'class' => 'primary'],
                                        'certification' => ['text' => 'Certificación', 'class' => 'success'],
                                        'course' => ['text' => 'Curso', 'class' => 'info'],
                                        'bootcamp' => ['text' => 'Bootcamp', 'class' => 'warning'],
                                        'workshop' => ['text' => 'Taller', 'class' => 'secondary']
                                    ];
                                    $type = $item['type'] ?? 'degree';
                                    $typeInfo = $typeLabels[$type] ?? $typeLabels['degree'];
                                @endphp
                                <span class="badge bg-{{ $typeInfo['class'] }}">
                                    {{ $typeInfo['text'] }}
                                </span>
                            </td>
                            <td class="align-middle">
                                @if(!empty($item['start_date']))
                                    @php
                                        $startDate = \Carbon\Carbon::parse($item['start_date']);
                                        $endDate = $item['end_date'] ? \Carbon\Carbon::parse($item['end_date']) : null;
                                    @endphp
                                    <div>
                                        <small class="text-muted">Inicio:</small> {{ $startDate->format('M Y') }}
                                        <br>
                                        @if($item['is_current'] ?? false)
                                            <span class="badge bg-success">En curso</span>
                                        @elseif($endDate)
                                            <small class="text-muted">Fin:</small> {{ $endDate->format('M Y') }}
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">Sin fecha</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($item['is_featured'] ?? false)
                                    <span class="badge bg-warning">Destacado</span>
                                @else
                                    <span class="badge bg-light text-dark">Normal</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.education.show', $item['id']) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.education.edit', $item['id']) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.education.destroy', $item['id']) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta formación?')">
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
                <i class="bi bi-mortarboard text-muted mb-3" style="font-size: 3rem;"></i>
                <h5 class="text-muted">No hay formación académica registrada</h5>
                <p class="text-muted mb-4">Comienza agregando tu educación, certificaciones o cursos.</p>
                <a href="{{ route('admin.education.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Agregar Primera Formación
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
