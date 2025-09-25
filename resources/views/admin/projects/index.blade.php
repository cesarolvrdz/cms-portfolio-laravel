@extends('layouts.admin')

@section('title', 'Proyectos')
@section('page-title', 'Gestión de Proyectos')
@section('page-description', 'Administra tu portfolio de proyectos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="bi bi-folder me-2"></i>
            Mis Proyectos
        </h4>
        <small class="text-muted">Total: {{ count($projects ?? []) }} proyectos</small>
    </div>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>
        Nuevo Proyecto
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if(!empty($projects) && count($projects) > 0)
            <!-- OPTIMIZACIÓN: Tabla optimizada para carga rápida -->
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="min-width: 700px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">Imagen</th>
                            <th>Proyecto</th>
                            <th style="width: 120px;">Estado</th>
                            <th style="width: 200px;">Tecnologías</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                        <tr id="project-{{ $project['id'] }}">
                            <td class="align-middle">
                                @if(!empty($project['image']))
                                    <!-- OPTIMIZACIÓN: Imagen lazy loading -->
                                    <img src="{{ $project['image'] }}" 
                                         alt="Imagen" 
                                         class="rounded" 
                                         style="width: 60px; height: 40px; object-fit: cover;"
                                         loading="lazy">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                         style="width: 60px; height: 40px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div>
                                    <h6 class="mb-1">{{ $project['title'] }}</h6>
                                    <small class="text-muted">
                                        {{ Str::limit($project['description'] ?? '', 50) }}
                                    </small>
                                </div>
                            </td>
                            <td class="align-middle">
                                @php
                                    $statusConfig = [
                                        'completed' => ['color' => 'success', 'icon' => 'check-circle', 'text' => 'Completado'],
                                        'in-progress' => ['color' => 'warning', 'icon' => 'clock', 'text' => 'En Progreso'],
                                        'planned' => ['color' => 'info', 'icon' => 'calendar', 'text' => 'Planeado']
                                    ];
                                    $config = $statusConfig[$project['status']] ?? ['color' => 'secondary', 'icon' => 'question', 'text' => 'Desconocido'];
                                @endphp
                                <span class="badge bg-{{ $config['color'] }}">
                                    <i class="bi bi-{{ $config['icon'] }} me-1"></i>
                                    {{ $config['text'] }}
                                </span>
                            </td>
                            <td class="align-middle">
                                @if(!empty($project['tech']) && is_array($project['tech']))
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach(array_slice($project['tech'], 0, 2) as $tech)
                                            <span class="badge bg-light text-dark">{{ $tech }}</span>
                                        @endforeach
                                        @if(count($project['tech']) > 2)
                                            <span class="text-muted small">+{{ count($project['tech']) - 2 }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <!-- OPTIMIZACIÓN: Botones optimizados -->
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.projects.edit', $project['id']) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger"
                                            title="Eliminar"
                                            onclick="deleteProject('{{ $project['id'] }}', '{{ addslashes($project['title']) }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-folder-x display-1 text-muted"></i>
                <h5 class="mt-3 text-muted">No hay proyectos</h5>
                <p class="text-muted mb-4">Comienza creando tu primer proyecto</p>
                <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Crear Proyecto
                </a>
            </div>
        @endif
    </div>
</div>

<!-- OPTIMIZACIÓN: JavaScript optimizado para eliminación -->
<script>
function deleteProject(id, title) {
    if (confirm(`¿Estás seguro de eliminar "${title}"?`)) {
        // Mostrar loading
        const row = document.getElementById(`project-${id}`);
        if (row) {
            row.style.opacity = '0.5';
            row.style.pointerEvents = 'none';
        }
        
        // Crear form dinámico
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('admin/projects') }}/${id}`;
        
        // CSRF Token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        
        // Method DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// OPTIMIZACIÓN: Preload de imágenes
document.addEventListener('DOMContentLoaded', function() {
    // Precargar la página de crear proyecto
    const createLink = document.querySelector('a[href*="create"]');
    if (createLink) {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = createLink.href;
        document.head.appendChild(link);
    }
});
</script>
@endsection
