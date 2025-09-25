@extends('layouts.admin')

@section('title', 'Vista Previa - ' . $cvDocument['title'])

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-eye text-primary me-2"></i>
                Vista Previa del CV
            </h1>
            <p class="text-muted">{{ $cvDocument['title'] }} - Versión {{ $cvDocument['version'] }}</p>
        </div>
        <div>
            <a href="{{ $cvDocument['file_url'] }}"
               target="_blank"
               class="btn btn-success me-2">
                <i class="fas fa-download me-1"></i>
                Descargar
            </a>
            <a href="{{ route('admin.cv.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Volver
            </a>
        </div>
    </div>

    <!-- Información del CV -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Información del Documento
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Título:</dt>
                        <dd class="col-sm-8">{{ $cvDocument['title'] }}</dd>

                        <dt class="col-sm-4">Versión:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-secondary">v{{ $cvDocument['version'] }}</span>
                        </dd>

                        <dt class="col-sm-4">Idioma:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-info">{{ strtoupper($cvDocument['language']) }}</span>
                        </dd>

                        <dt class="col-sm-4">Estado:</dt>
                        <dd class="col-sm-8">
                            @if($cvDocument['is_current'])
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>
                                    CV Actual
                                </span>
                            @else
                                <span class="badge bg-secondary">CV Archivado</span>
                            @endif
                        </dd>

                        @if($cvDocument['description'])
                        <dt class="col-sm-4">Descripción:</dt>
                        <dd class="col-sm-8">{{ $cvDocument['description'] }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estadísticas
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Fecha de subida:</dt>
                        <dd class="col-sm-6">{{ \Carbon\Carbon::parse($cvDocument['created_at'])->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-6">Última actualización:</dt>
                        <dd class="col-sm-6">{{ \Carbon\Carbon::parse($cvDocument['updated_at'])->diffForHumans() }}</dd>

                        @if($cvDocument['file_size'])
                        <dt class="col-sm-6">Tamaño del archivo:</dt>
                        <dd class="col-sm-6">{{ number_format($cvDocument['file_size'] / 1024 / 1024, 1) }} MB</dd>
                        @endif

                        <dt class="col-sm-6">Descargas:</dt>
                        <dd class="col-sm-6">
                            <span class="badge bg-primary">{{ $cvDocument['download_count'] ?? 0 }}</span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Visor de PDF -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-pdf me-2"></i>
                    Documento PDF
                </h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="zoomOut()">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <span class="btn btn-sm btn-outline-secondary" id="zoom-level">100%</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="zoomIn()">
                        <i class="fas fa-search-plus"></i>
                    </button>
                    <a href="{{ $cvDocument['file_url'] }}"
                       target="_blank"
                       class="btn btn-sm btn-primary">
                        <i class="fas fa-external-link-alt me-1"></i>
                        Abrir en nueva pestaña
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="pdf-container" style="height: 800px; overflow: auto;">
                <iframe id="pdf-viewer"
                        src="{{ $cvDocument['file_url'] }}#zoom=100"
                        width="100%"
                        height="100%"
                        frameborder="0"
                        style="border: none;">
                    <p>Tu navegador no soporta la visualización de PDFs.
                       <a href="{{ $cvDocument['file_url'] }}" target="_blank">Haz clic aquí para descargar el CV</a>.
                    </p>
                </iframe>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('admin.cv.edit', $cvDocument['id']) }}"
                           class="btn btn-outline-primary">
                            <i class="fas fa-edit me-1"></i>
                            Editar CV
                        </a>

                        @if(!$cvDocument['is_current'])
                        <button class="btn btn-outline-warning"
                                onclick="setCurrent({{ $cvDocument['id'] }})">
                            <i class="fas fa-star me-1"></i>
                            Establecer como Actual
                        </button>
                        @endif

                        <a href="{{ $cvDocument['file_url'] }}"
                           class="btn btn-outline-success"
                           download>
                            <i class="fas fa-download me-1"></i>
                            Descargar
                        </a>

                        <button class="btn btn-outline-info"
                                onclick="copyLink()">
                            <i class="fas fa-link me-1"></i>
                            Copiar Enlace
                        </button>

                        <form method="POST"
                              action="{{ route('admin.cv.destroy', $cvDocument['id']) }}"
                              class="d-inline"
                              onsubmit="return confirm('¿Estás seguro de eliminar este CV?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash me-1"></i>
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentZoom = 100;

function zoomIn() {
    currentZoom = Math.min(currentZoom + 25, 200);
    updateZoom();
}

function zoomOut() {
    currentZoom = Math.max(currentZoom - 25, 50);
    updateZoom();
}

function updateZoom() {
    const iframe = document.getElementById('pdf-viewer');
    const zoomLevel = document.getElementById('zoom-level');

    iframe.src = iframe.src.split('#')[0] + '#zoom=' + currentZoom;
    zoomLevel.textContent = currentZoom + '%';
}

function setCurrent(id) {
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
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión');
        });
    }
}

function copyLink() {
    const link = '{{ $cvDocument["file_url"] }}';
    navigator.clipboard.writeText(link).then(function() {
        // Mostrar notificación de éxito
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            Enlace copiado al portapapeles
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 3000);
    }, function(err) {
        alert('Error al copiar el enlace: ' + err);
    });
}
</script>
@endpush
@endsection
