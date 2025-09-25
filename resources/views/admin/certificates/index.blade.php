@extends('layouts.admin')

@section('title', 'Gestión de Certificados')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-certificate text-primary me-2"></i>
                Certificados y Cursos
            </h1>
            <p class="text-muted">Gestiona los certificados y cursos que aparecen en tu portafolio</p>
        </div>
        <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Nuevo Certificado
        </a>
    </div>

    <!-- Filtros activos -->
    @if(request('category') || request('featured'))
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="fas fa-filter me-2"></i>
            <span class="me-auto">
                Filtros activos:
                @if(request('category'))
                    <span class="badge bg-primary ms-1">{{ ucfirst(request('category')) }}</span>
                @endif
                @if(request('featured') == '1')
                    <span class="badge bg-warning ms-1">Destacados</span>
                @elseif(request('featured') == '0')
                    <span class="badge bg-secondary ms-1">No destacados</span>
                @endif
            </span>
            <a href="{{ route('admin.certificates.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-times me-1"></i>Limpiar filtros
            </a>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="category" class="form-label">Categoría</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="featured" class="form-label">Estado</label>
                    <select class="form-select" id="featured" name="featured">
                        <option value="">Todos</option>
                        <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Destacados</option>
                        <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>No destacados</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-filter me-1"></i>
                        Filtrar
                    </button>
                    <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Certificados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($certificates) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
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
                                Destacados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ count(array_filter($certificates, fn($c) => $c['is_featured'] ?? false)) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
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
                                Categorías
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($categories) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
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
                                Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ count(array_filter($certificates, fn($c) => $c['is_active'] ?? false)) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de certificados -->
    @if(count($certificates) > 0)
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Imagen</th>
                                <th>Certificado</th>
                                <th>Institución</th>
                                <th>Categoría</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificates as $certificate)
                            <tr>
                                <td>
                                    @if($certificate['image_url'])
                                        <img src="{{ $certificate['image_url'] }}"
                                             alt="{{ $certificate['title'] }}"
                                             class="img-thumbnail"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-certificate text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">{{ $certificate['title'] }}</h6>
                                        @if($certificate['skills'])
                                            <div class="skills-tags">
                                                @foreach(is_array($certificate['skills']) ? $certificate['skills'] : [] as $skill)
                                                    <span class="badge bg-light text-dark me-1">{{ $skill }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $certificate['institution'] }}</strong>
                                    @if($certificate['credential_id'])
                                        <br>
                                        <small class="text-muted">ID: {{ $certificate['credential_id'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($certificate['category']) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($certificate['issue_date'])->format('M Y') }}
                                    </small>
                                    @if($certificate['expiry_date'])
                                        <br>
                                        <small class="text-warning">
                                            Expira: {{ \Carbon\Carbon::parse($certificate['expiry_date'])->format('M Y') }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <!-- Estado Activo -->
                                        <button class="btn btn-sm toggle-active {{ $certificate['is_active'] ? 'btn-success' : 'btn-outline-secondary' }}"
                                                data-id="{{ $certificate['id'] }}">
                                            <i class="fas fa-eye{{ $certificate['is_active'] ? '' : '-slash' }}"></i>
                                            {{ $certificate['is_active'] ? 'Activo' : 'Inactivo' }}
                                        </button>

                                        <!-- Estado Destacado -->
                                        <button class="btn btn-sm toggle-featured {{ $certificate['is_featured'] ? 'btn-warning' : 'btn-outline-warning' }}"
                                                data-id="{{ $certificate['id'] }}">
                                            <i class="fas fa-star"></i>
                                            {{ $certificate['is_featured'] ? 'Destacado' : 'Normal' }}
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($certificate['pdf_url'])
                                            <a href="{{ $certificate['pdf_url'] }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-info"
                                               title="Ver PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @endif

                                        @if($certificate['credential_url'])
                                            <a href="{{ $certificate['credential_url'] }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Verificar certificado">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.certificates.edit', $certificate['id']) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form method="POST"
                                              action="{{ route('admin.certificates.destroy', $certificate['id']) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este certificado?')">
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
                <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay certificados registrados</h5>
                <p class="text-muted">Comienza agregando tu primer certificado o curso.</p>
                <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Agregar Certificado
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const filterForm = document.querySelector('form[method="GET"]');
    const categorySelect = document.getElementById('category');
    const featuredSelect = document.getElementById('featured');

    function submitFilterForm() {
        filterForm.submit();
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', submitFilterForm);
    }

    if (featuredSelect) {
        featuredSelect.addEventListener('change', submitFilterForm);
    }

    // Toggle Active Status
    document.querySelectorAll('.toggle-active').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;

            fetch(`/admin/certificates/${id}/toggle-active`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_active) {
                        this.className = 'btn btn-sm toggle-active btn-success';
                        this.innerHTML = '<i class="fas fa-eye"></i> Activo';
                    } else {
                        this.className = 'btn btn-sm toggle-active btn-outline-secondary';
                        this.innerHTML = '<i class="fas fa-eye-slash"></i> Inactivo';
                    }

                    // Show toast notification
                    showToast('Estado actualizado exitosamente', 'success');
                } else {
                    showToast('Error al actualizar el estado', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error de conexión', 'error');
            });
        });
    });

    // Toggle Featured Status
    document.querySelectorAll('.toggle-featured').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;

            fetch(`/admin/certificates/${id}/toggle-featured`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_featured) {
                        this.className = 'btn btn-sm toggle-featured btn-warning';
                        this.innerHTML = '<i class="fas fa-star"></i> Destacado';
                    } else {
                        this.className = 'btn btn-sm toggle-featured btn-outline-warning';
                        this.innerHTML = '<i class="fas fa-star"></i> Normal';
                    }

                    showToast('Estado actualizado exitosamente', 'success');
                } else {
                    showToast('Error al actualizar el estado', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error de conexión', 'error');
            });
        });
    });
});

function showToast(message, type) {
    // Implementación simple de toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(toast);

    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}
</script>
@endpush
@endsection
