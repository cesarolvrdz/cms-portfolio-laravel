@extends('layouts.admin')

@section('title', 'Crear Enlace Social')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-plus-circle text-success me-2"></i>Crear Enlace Social
            </h1>
            <p class="text-muted">Agrega un nuevo enlace social a tu portafolio</p>
        </div>
        <a href="{{ route('admin.social.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <h6><i class="bi bi-exclamation-triangle me-2"></i>Errores de validación:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="bi bi-link me-2"></i>Información del Enlace
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.social.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="platform" class="form-label">
                                    <i class="bi bi-tag me-1"></i>Plataforma *
                                </label>
                                <input type="text" class="form-control" id="platform" name="platform"
                                       value="{{ old('platform') }}" required>
                                <div class="form-text">Ejemplo: GitHub, LinkedIn, Twitter</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="icon" class="form-label">
                                    <i class="bi bi-image me-1"></i>Icono (Bootstrap Icons)
                                </label>
                                <input type="text" class="form-control" id="icon" name="icon"
                                       value="{{ old('icon') }}" placeholder="bi-github">
                                <div class="form-text">Ejemplo: bi-github, bi-linkedin, bi-twitter</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="url" class="form-label">
                                <i class="bi bi-link-45deg me-1"></i>URL *
                            </label>
                            <input type="url" class="form-control" id="url" name="url"
                                   value="{{ old('url') }}" required placeholder="https://github.com/usuario">
                            <div class="form-text">URL completa del enlace social</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="color" class="form-label">
                                    <i class="bi bi-palette me-1"></i>Color (Opcional)
                                </label>
                                <input type="color" class="form-control form-control-color" id="color"
                                       name="color" value="{{ old('color', '#007bff') }}">
                                <div class="form-text">Color característico de la plataforma</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="order" class="form-label">
                                    <i class="bi bi-arrow-up-down me-1"></i>Orden
                                </label>
                                <input type="number" class="form-control" id="order" name="order"
                                       value="{{ old('order', 0) }}" min="0">
                                <div class="form-text">Orden de aparición (menor número = primero)</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="bi bi-eye me-1"></i>Enlace activo (visible en el portafolio)
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.social.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i>Crear Enlace
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Preview -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="bi bi-eye me-2"></i>Vista Previa
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center" id="preview">
                        <div class="social-link-preview mb-3">
                            <i class="bi bi-link text-primary" style="font-size: 2rem; color: #007bff !important;"></i>
                        </div>
                        <h6 class="mb-2">Plataforma</h6>
                        <p class="text-muted small mb-0">https://ejemplo.com</p>
                        <span class="badge bg-success mt-2">Activo</span>
                    </div>
                </div>
            </div>

            <!-- Plantillas rápidas -->
            <div class="card shadow mt-3">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="bi bi-lightning me-2"></i>Plantillas Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-outline-dark btn-sm w-100"
                                    onclick="fillTemplate('GitHub', 'bi-github', '#181717', 'https://github.com/')">
                                <i class="bi bi-github me-1"></i>GitHub
                            </button>
                        </div>
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100"
                                    onclick="fillTemplate('LinkedIn', 'bi-linkedin', '#0077B5', 'https://linkedin.com/in/')">
                                <i class="bi bi-linkedin me-1"></i>LinkedIn
                            </button>
                        </div>
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-outline-info btn-sm w-100"
                                    onclick="fillTemplate('Twitter', 'bi-twitter', '#1DA1F2', 'https://twitter.com/')">
                                <i class="bi bi-twitter me-1"></i>Twitter
                            </button>
                        </div>
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                    onclick="fillTemplate('Email', 'bi-envelope', '#EA4335', 'mailto:')">
                                <i class="bi bi-envelope me-1"></i>Email
                            </button>
                        </div>
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-outline-warning btn-sm w-100"
                                    onclick="fillTemplate('Instagram', 'bi-instagram', '#E4405F', 'https://instagram.com/')">
                                <i class="bi bi-instagram me-1"></i>Instagram
                            </button>
                        </div>
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-outline-success btn-sm w-100"
                                    onclick="fillTemplate('Portfolio', 'bi-globe', '#28a745', 'https://')">
                                <i class="bi bi-globe me-1"></i>Web
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Iconos comunes -->
            <div class="card shadow mt-3">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="bi bi-info-circle me-2"></i>Iconos Disponibles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4 mb-2">
                            <i class="bi bi-github" style="font-size: 1.5rem;"></i>
                            <small class="d-block">bi-github</small>
                        </div>
                        <div class="col-4 mb-2">
                            <i class="bi bi-linkedin" style="font-size: 1.5rem;"></i>
                            <small class="d-block">bi-linkedin</small>
                        </div>
                        <div class="col-4 mb-2">
                            <i class="bi bi-twitter" style="font-size: 1.5rem;"></i>
                            <small class="d-block">bi-twitter</small>
                        </div>
                        <div class="col-4 mb-2">
                            <i class="bi bi-envelope" style="font-size: 1.5rem;"></i>
                            <small class="d-block">bi-envelope</small>
                        </div>
                        <div class="col-4 mb-2">
                            <i class="bi bi-instagram" style="font-size: 1.5rem;"></i>
                            <small class="d-block">bi-instagram</small>
                        </div>
                        <div class="col-4 mb-2">
                            <i class="bi bi-globe" style="font-size: 1.5rem;"></i>
                            <small class="d-block">bi-globe</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar preview en tiempo real
    const inputs = ['platform', 'icon', 'color', 'url', 'is_active'];

    inputs.forEach(inputName => {
        const input = document.getElementById(inputName);
        if (input) {
            input.addEventListener('input', updatePreview);
            input.addEventListener('change', updatePreview);
        }
    });

    function updatePreview() {
        const platform = document.getElementById('platform').value || 'Plataforma';
        const icon = document.getElementById('icon').value || 'bi-link';
        const color = document.getElementById('color').value || '#007bff';
        const url = document.getElementById('url').value || 'https://ejemplo.com';
        const isActive = document.getElementById('is_active').checked;

        const preview = document.getElementById('preview');
        preview.innerHTML = `
            <div class="social-link-preview mb-3">
                <i class="bi ${icon} text-primary" style="font-size: 2rem; color: ${color} !important;"></i>
            </div>
            <h6 class="mb-2">${platform}</h6>
            <p class="text-muted small mb-0">${url}</p>
            <span class="badge ${isActive ? 'bg-success' : 'bg-secondary'} mt-2">
                ${isActive ? 'Activo' : 'Inactivo'}
            </span>
        `;
    }

    // Función global para plantillas rápidas
    window.fillTemplate = function(platform, icon, color, urlPrefix) {
        document.getElementById('platform').value = platform;
        document.getElementById('icon').value = icon;
        document.getElementById('color').value = color;
        document.getElementById('url').value = urlPrefix;
        updatePreview();
    };
});
</script>
@endsection
