@extends('layouts.admin')

@section('title', 'Nueva Experiencia Laboral')
@section('page-title', 'Agregar Experiencia Laboral')
@section('page-description', 'Registra tu historial profesional y laboral')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-briefcase me-2"></i>
                    Información de la Experiencia
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.experience.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="company" class="form-label">
                                    <i class="bi bi-building me-1"></i>
                                    Empresa <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="company" id="company"
                                       class="form-control @error('company') is-invalid @enderror"
                                       value="{{ old('company') }}"
                                       placeholder="Ej: Google, StartupXYZ, Freelance" required>
                                @error('company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="employment_type" class="form-label">
                                    <i class="bi bi-clock me-1"></i>
                                    Tipo de Empleo
                                </label>
                                <select name="employment_type" id="employment_type" class="form-select @error('employment_type') is-invalid @enderror">
                                    <option value="full-time" {{ old('employment_type') == 'full-time' ? 'selected' : '' }}>Tiempo Completo</option>
                                    <option value="part-time" {{ old('employment_type') == 'part-time' ? 'selected' : '' }}>Tiempo Parcial</option>
                                    <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Contrato</option>
                                    <option value="freelance" {{ old('employment_type') == 'freelance' ? 'selected' : '' }}>Freelance</option>
                                    <option value="internship" {{ old('employment_type') == 'internship' ? 'selected' : '' }}>Prácticas</option>
                                    <option value="volunteer" {{ old('employment_type') == 'volunteer' ? 'selected' : '' }}>Voluntario</option>
                                </select>
                                @error('employment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="position" class="form-label">
                                    <i class="bi bi-person-badge me-1"></i>
                                    Cargo/Posición <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="position" id="position"
                                       class="form-control @error('position') is-invalid @enderror"
                                       value="{{ old('position') }}"
                                       placeholder="Ej: Desarrollador Full Stack, Gerente de Proyectos" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="department" class="form-label">
                                    <i class="bi bi-diagram-3 me-1"></i>
                                    Departamento
                                </label>
                                <input type="text" name="department" id="department"
                                       class="form-control @error('department') is-invalid @enderror"
                                       value="{{ old('department') }}"
                                       placeholder="Ej: Tecnología, Marketing, RRHH">
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">
                                    <i class="bi bi-calendar me-1"></i>
                                    Fecha de Inicio <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="start_date" id="start_date"
                                       class="form-control @error('start_date') is-invalid @enderror"
                                       value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    Fecha de Finalización
                                </label>
                                <input type="date" name="end_date" id="end_date"
                                       class="form-control @error('end_date') is-invalid @enderror"
                                       value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="form-check">
                                    <input type="checkbox" name="is_current" id="is_current"
                                           class="form-check-input" value="1"
                                           {{ old('is_current') ? 'checked' : '' }}>
                                    <label for="is_current" class="form-check-label">
                                        <i class="bi bi-briefcase-fill me-1"></i>
                                        Trabajo actual
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="location" class="form-label">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    Ubicación
                                </label>
                                <input type="text" name="location" id="location"
                                       class="form-control @error('location') is-invalid @enderror"
                                       value="{{ old('location') }}"
                                       placeholder="Ej: Ciudad de México, México">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="location_type" class="form-label">
                                    <i class="bi bi-house me-1"></i>
                                    Modalidad
                                </label>
                                <select name="location_type" id="location_type" class="form-select @error('location_type') is-invalid @enderror">
                                    <option value="on-site" {{ old('location_type') == 'on-site' ? 'selected' : '' }}>Presencial</option>
                                    <option value="remote" {{ old('location_type') == 'remote' ? 'selected' : '' }}>Remoto</option>
                                    <option value="hybrid" {{ old('location_type') == 'hybrid' ? 'selected' : '' }}>Híbrido</option>
                                </select>
                                @error('location_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="bi bi-file-text me-1"></i>
                            Descripción del Trabajo
                        </label>
                        <textarea name="description" id="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Describe tu rol, responsabilidades principales, el contexto del trabajo...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="responsibilities" class="form-label">
                            <i class="bi bi-list-check me-1"></i>
                            Responsabilidades Principales
                        </label>
                        <div id="responsibilities-container">
                            @if(old('responsibilities'))
                                @foreach(old('responsibilities') as $index => $responsibility)
                                    <div class="input-group mb-2 responsibility-item">
                                        <input type="text" name="responsibilities[]"
                                               class="form-control"
                                               value="{{ $responsibility }}"
                                               placeholder="Ej: Desarrollo de aplicaciones web con Laravel y React">
                                        <button type="button" class="btn btn-outline-danger remove-responsibility">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2 responsibility-item">
                                    <input type="text" name="responsibilities[]"
                                           class="form-control"
                                           placeholder="Ej: Desarrollo de aplicaciones web con Laravel y React">
                                    <button type="button" class="btn btn-outline-danger remove-responsibility">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-responsibility">
                            <i class="bi bi-plus me-1"></i>
                            Agregar Responsabilidad
                        </button>
                    </div>

                    <div class="mb-3">
                        <label for="achievements" class="form-label">
                            <i class="bi bi-trophy me-1"></i>
                            Logros y Resultados
                        </label>
                        <div id="achievements-container">
                            @if(old('achievements'))
                                @foreach(old('achievements') as $index => $achievement)
                                    <div class="input-group mb-2 achievement-item">
                                        <input type="text" name="achievements[]"
                                               class="form-control"
                                               value="{{ $achievement }}"
                                               placeholder="Ej: Reducción del 30% en tiempo de carga de la aplicación">
                                        <button type="button" class="btn btn-outline-danger remove-achievement">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2 achievement-item">
                                    <input type="text" name="achievements[]"
                                           class="form-control"
                                           placeholder="Ej: Reducción del 30% en tiempo de carga de la aplicación">
                                    <button type="button" class="btn btn-outline-danger remove-achievement">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-success btn-sm" id="add-achievement">
                            <i class="bi bi-plus me-1"></i>
                            Agregar Logro
                        </button>
                    </div>

                    <div class="mb-3">
                        <label for="technologies" class="form-label">
                            <i class="bi bi-tools me-1"></i>
                            Tecnologías y Herramientas
                        </label>
                        <div id="technologies-container">
                            @if(old('technologies'))
                                @foreach(old('technologies') as $index => $technology)
                                    <div class="input-group mb-2 technology-item">
                                        <input type="text" name="technologies[]"
                                               class="form-control"
                                               value="{{ $technology }}"
                                               placeholder="Ej: Laravel, React, Docker">
                                        <button type="button" class="btn btn-outline-danger remove-technology">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2 technology-item">
                                    <input type="text" name="technologies[]"
                                           class="form-control"
                                           placeholder="Ej: Laravel, React, Docker">
                                    <button type="button" class="btn btn-outline-danger remove-technology">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-info btn-sm" id="add-technology">
                            <i class="bi bi-plus me-1"></i>
                            Agregar Tecnología
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_url" class="form-label">
                                    <i class="bi bi-link me-1"></i>
                                    URL de la Empresa
                                </label>
                                <input type="url" name="company_url" id="company_url"
                                       class="form-control @error('company_url') is-invalid @enderror"
                                       value="{{ old('company_url') }}"
                                       placeholder="https://www.empresa.com">
                                @error('company_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_logo_url" class="form-label">
                                    <i class="bi bi-image me-1"></i>
                                    URL del Logo de la Empresa
                                </label>
                                <input type="url" name="company_logo_url" id="company_logo_url"
                                       class="form-control @error('company_logo_url') is-invalid @enderror"
                                       value="{{ old('company_logo_url') }}"
                                       placeholder="https://www.empresa.com/logo.png">
                                @error('company_logo_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">O sube una imagen abajo</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="company_logo" class="form-label">
                            <i class="bi bi-upload me-1"></i>
                            Subir Logo de la Empresa
                        </label>
                        <input type="file" name="company_logo" id="company_logo"
                               class="form-control @error('company_logo') is-invalid @enderror"
                               accept="image/jpeg,image/jpg,image/png,image/webp">
                        @error('company_logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Formatos: JPG, PNG, WebP. Máximo 2MB.</small>
                    </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_featured" id="is_featured"
                                       class="form-check-input" value="1"
                                       {{ old('is_featured') ? 'checked' : '' }}>
                                <label for="is_featured" class="form-check-label">
                                    <i class="bi bi-star me-1"></i>
                                    Destacar en el portafolio
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="order_position" class="form-label">
                                    <i class="bi bi-sort-numeric-down me-1"></i>
                                    Orden de visualización
                                </label>
                                <input type="number" name="order_position" id="order_position"
                                       class="form-control @error('order_position') is-invalid @enderror"
                                       value="{{ old('order_position', 0) }}" min="0">
                                @error('order_position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.experience.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>
                            Guardar Experiencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-info-circle me-2"></i>
                    Consejos
                </h6>
                <ul class="small text-muted">
                    <li class="mb-2">
                        <strong>Empresa:</strong> Nombre completo de la organización donde trabajaste.
                    </li>
                    <li class="mb-2">
                        <strong>Cargo:</strong> Tu título o posición oficial en la empresa.
                    </li>
                    <li class="mb-2">
                        <strong>Trabajo actual:</strong> Marca esta opción si aún trabajas aquí.
                    </li>
                    <li class="mb-2">
                        <strong>Responsabilidades:</strong> Lista las tareas y funciones principales.
                    </li>
                    <li class="mb-2">
                        <strong>Logros:</strong> Resultados cuantificables y éxitos alcanzados.
                    </li>
                    <li>
                        <strong>Tecnologías:</strong> Herramientas, lenguajes y tecnologías utilizadas.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad para trabajo actual
    const isCurrentCheckbox = document.getElementById('is_current');
    const endDateInput = document.getElementById('end_date');

    isCurrentCheckbox.addEventListener('change', function() {
        if (this.checked) {
            endDateInput.value = '';
            endDateInput.disabled = true;
        } else {
            endDateInput.disabled = false;
        }
    });

    // Inicializar el estado
    if (isCurrentCheckbox.checked) {
        endDateInput.disabled = true;
    }

    // Funcionalidad para agregar/quitar responsabilidades
    document.getElementById('add-responsibility').addEventListener('click', function() {
        const container = document.getElementById('responsibilities-container');
        const newItem = container.querySelector('.responsibility-item').cloneNode(true);
        newItem.querySelector('input').value = '';
        container.appendChild(newItem);

        // Agregar event listener al botón de eliminar
        newItem.querySelector('.remove-responsibility').addEventListener('click', function() {
            if (container.children.length > 1) {
                newItem.remove();
            }
        });
    });

    // Event listeners para botones de eliminar responsabilidades existentes
    document.querySelectorAll('.remove-responsibility').forEach(button => {
        button.addEventListener('click', function() {
            const container = document.getElementById('responsibilities-container');
            if (container.children.length > 1) {
                button.closest('.responsibility-item').remove();
            }
        });
    });

    // Funcionalidad para agregar/quitar logros
    document.getElementById('add-achievement').addEventListener('click', function() {
        const container = document.getElementById('achievements-container');
        const newItem = container.querySelector('.achievement-item').cloneNode(true);
        newItem.querySelector('input').value = '';
        container.appendChild(newItem);

        newItem.querySelector('.remove-achievement').addEventListener('click', function() {
            if (container.children.length > 1) {
                newItem.remove();
            }
        });
    });

    document.querySelectorAll('.remove-achievement').forEach(button => {
        button.addEventListener('click', function() {
            const container = document.getElementById('achievements-container');
            if (container.children.length > 1) {
                button.closest('.achievement-item').remove();
            }
        });
    });

    // Funcionalidad para agregar/quitar tecnologías
    document.getElementById('add-technology').addEventListener('click', function() {
        const container = document.getElementById('technologies-container');
        const newItem = container.querySelector('.technology-item').cloneNode(true);
        newItem.querySelector('input').value = '';
        container.appendChild(newItem);

        newItem.querySelector('.remove-technology').addEventListener('click', function() {
            if (container.children.length > 1) {
                newItem.remove();
            }
        });
    });

    document.querySelectorAll('.remove-technology').forEach(button => {
        button.addEventListener('click', function() {
            const container = document.getElementById('technologies-container');
            if (container.children.length > 1) {
                button.closest('.technology-item').remove();
            }
        });
    });
});
</script>
@endsection
