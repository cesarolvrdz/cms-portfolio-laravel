@extends('layouts.admin')

@section('title', 'Subir Nuevo CV')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-upload text-primary me-2"></i>
                Subir Nuevo CV
            </h1>
            <p class="text-muted">Agrega una nueva versión de tu CV</p>
        </div>
        <a href="{{ route('admin.cv.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Volver a la lista
        </a>
    </div>

    <form action="{{ route('admin.cv.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Información Principal -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Información del CV
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Título del CV *</label>
                                <input type="text"
                                       class="form-control @error('title') is-invalid @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title', 'Mi CV') }}"
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="version" class="form-label">Versión</label>
                                <input type="text"
                                       class="form-control @error('version') is-invalid @enderror"
                                       id="version"
                                       name="version"
                                       value="{{ old('version', '1.0') }}"
                                       placeholder="1.0">
                                @error('version')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="language" class="form-label">Idioma *</label>
                                <select class="form-select @error('language') is-invalid @enderror"
                                        id="language"
                                        name="language"
                                        required>
                                    <option value="es" {{ old('language', 'es') == 'es' ? 'selected' : '' }}>Español</option>
                                    <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="fr" {{ old('language') == 'fr' ? 'selected' : '' }}>Français</option>
                                    <option value="de" {{ old('language') == 'de' ? 'selected' : '' }}>Deutsch</option>
                                    <option value="pt" {{ old('language') == 'pt' ? 'selected' : '' }}>Português</option>
                                </select>
                                @error('language')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Describe esta versión del CV (ej: CV actualizado con nuevos proyectos, CV en inglés para aplicaciones internacionales, etc.)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Lateral -->
            <div class="col-lg-4">
                <!-- Archivo PDF -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-pdf me-2"></i>
                            Archivo PDF *
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="file"
                                   class="form-control @error('pdf') is-invalid @enderror"
                                   id="pdf"
                                   name="pdf"
                                   accept=".pdf"
                                   required>
                            <div class="form-text">Archivo PDF del CV (máximo 20MB)</div>
                            @error('pdf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Información del archivo seleccionado -->
                        <div id="file-info" class="alert alert-info" style="display: none;">
                            <h6 class="alert-heading">Archivo seleccionado:</h6>
                            <p class="mb-1"><strong>Nombre:</strong> <span id="file-name"></span></p>
                            <p class="mb-0"><strong>Tamaño:</strong> <span id="file-size"></span></p>
                        </div>

                        <!-- Consejos -->
                        <div class="alert alert-light">
                            <h6 class="alert-heading"><i class="fas fa-lightbulb me-1"></i> Consejos:</h6>
                            <ul class="mb-0 small">
                                <li>Usa un nombre descriptivo para el archivo</li>
                                <li>Asegúrate de que el PDF sea legible</li>
                                <li>Mantén el tamaño del archivo razonable</li>
                                <li>Incluye información de contacto actualizada</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Configuración -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>
                            Configuración
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="is_current"
                                   name="is_current"
                                   value="1"
                                   {{ old('is_current') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_current">
                                <i class="fas fa-star text-warning me-1"></i>
                                Establecer como CV actual
                            </label>
                            <div class="form-text">El CV actual es el que se descarga desde el portafolio</div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-1"></i>
                                Subir CV
                            </button>
                            <a href="{{ route('admin.cv.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar información del archivo seleccionado
    const pdfInput = document.getElementById('pdf');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');

    pdfInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.style.display = 'block';

            // Validar tamaño del archivo (20MB)
            if (file.size > 20 * 1024 * 1024) {
                alert('El archivo es demasiado grande. El tamaño máximo es 20MB.');
                pdfInput.value = '';
                fileInfo.style.display = 'none';
            }
        } else {
            fileInfo.style.display = 'none';
        }
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Validación del formulario
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const language = document.getElementById('language').value;
        const pdf = document.getElementById('pdf').files[0];

        if (!title || !language || !pdf) {
            e.preventDefault();
            alert('Por favor completa todos los campos obligatorios.');
            return;
        }

        if (pdf && pdf.type !== 'application/pdf') {
            e.preventDefault();
            alert('Por favor selecciona un archivo PDF válido.');
            return;
        }
    });
});
</script>
@endpush
@endsection
