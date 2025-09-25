@extends('layouts.admin')

@section('title', 'Gestión de CV')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-pdf text-primary me-2"></i>
                Gestión de CV
            </h1>
            <p class="text-muted">Administra las versiones de tu CV disponibles para descarga</p>
        </div>
        <a href="{{ route('admin.cv.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Subir Nuevo CV
        </a>
    </div>

    <!-- CV Actual -->
    @if($currentCv)
    <div class="alert alert-success border-left-success mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle fa-2x text-success me-3"></i>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-1">CV Actual: {{ $currentCv['title'] }}</h5>
                <p class="mb-2">{{ $currentCv['description'] ?? 'Sin descripción' }}</p>
                <small class="text-muted">
                    Versión {{ $currentCv['version'] }} |
                    Idioma: {{ strtoupper($currentCv['language']) }} |
                    Subido: {{ \Carbon\Carbon::parse($currentCv['created_at'])->format('d/m/Y H:i') }}
                    @if($currentCv['download_count'] > 0)
                        | Descargas: {{ $currentCv['download_count'] }}
                    @endif
                </small>
            </div>
            <div class="ms-3">
                <a href="{{ $currentCv['file_url'] }}" target="_blank" class="btn btn-outline-success btn-sm me-2">
                    <i class="fas fa-download me-1"></i>
                    Descargar
                </a>
                <a href="{{ route('admin.cv.preview', $currentCv['id']) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye me-1"></i>
                    Vista Previa
                </a>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning border-left-warning mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
            <div>
                <h5 class="alert-heading mb-1">No hay CV actual establecido</h5>
                <p class="mb-0">Sube un CV y márcalo como actual para que esté disponible en tu portafolio.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total CVs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($cvDocuments) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-pdf fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Descargas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ array_sum(array_column($cvDocuments, 'download_count')) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-download fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Idiomas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ count(array_unique(array_column($cvDocuments, 'language'))) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-globe fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Última Actualización
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                @if(count($cvDocuments) > 0)
                                    {{ \Carbon\Carbon::parse(collect($cvDocuments)->max('updated_at'))->diffForHumans() }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de CVs -->
    @if(count($cvDocuments) > 0)
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Documento</th>
                                <th>Versión</th>
                                <th>Idioma</th>
                                <th>Fecha</th>
                                <th>Tamaño</th>
                                <th>Descargas</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cvDocuments as $cv)
                            <tr class="{{ $cv['is_current'] ? 'table-success' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                        <div>
                                            <h6 class="mb-0">{{ $cv['title'] }}</h6>
                                            @if($cv['description'])
                                                <small class="text-muted">{{ Str::limit($cv['description'], 50) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">v{{ $cv['version'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ strtoupper($cv['language']) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($cv['created_at'])->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        @if($cv['file_size'])
                                            {{ number_format($cv['file_size'] / 1024 / 1024, 1) }} MB
                                        @else
                                            N/A
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $cv['download_count'] ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    @if($cv['is_current'])
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>
                                            Actual
                                        </span>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary set-current"
                                                data-id="{{ $cv['id'] }}">
                                            <i class="fas fa-star"></i>
                                            Establecer como actual
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.cv.preview', $cv['id']) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Vista previa">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.cv.download', $cv['id']) }}"
                                           class="btn btn-sm btn-outline-success"
                                           title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        <a href="{{ route('admin.cv.edit', $cv['id']) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form method="POST"
                                              action="{{ route('admin.cv.destroy', $cv['id']) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este CV?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-pdf fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay documentos CV registrados</h5>
                <p class="text-muted">Sube tu primer CV para que esté disponible en tu portafolio.</p>
                <a href="{{ route('admin.cv.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Subir CV
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Establecer como CV actual
    document.querySelectorAll('.set-current').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;

            if (confirm('¿Establecer este CV como el actual? Esto desactivará el CV actual.')) {
                fetch(`/admin/cv/${id}/set-current`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Recargar para actualizar el estado
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                });
            }
        });
    });
});
</script>
@endpush
@endsection
