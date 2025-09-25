@extends('layouts.admin')

@section('title', 'Editar Experiencia Laboral')
@section('page-title', 'Editar Experiencia Laboral')
@section('page-description', 'Actualiza la información de tu experiencia profesional')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    Editar Información de la Experiencia
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.experience.update', $experience['id']) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="company" class="form-label">
                                    <i class="bi bi-building me-1"></i>
                                    Empresa <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="company" id="company"
                                       class="form-control @error('company') is-invalid @enderror"
                                       value="{{ old('company', $experience['company'] ?? '') }}"
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
                                    <option value="full-time" {{ old('employment_type', $experience['employment_type'] ?? '') == 'full-time' ? 'selected' : '' }}>Tiempo Completo</option>
                                    <option value="part-time" {{ old('employment_type', $experience['employment_type'] ?? '') == 'part-time' ? 'selected' : '' }}>Tiempo Parcial</option>
                                    <option value="contract" {{ old('employment_type', $experience['employment_type'] ?? '') == 'contract' ? 'selected' : '' }}>Contrato</option>
                                    <option value="freelance" {{ old('employment_type', $experience['employment_type'] ?? '') == 'freelance' ? 'selected' : '' }}>Freelance</option>
                                    <option value="internship" {{ old('employment_type', $experience['employment_type'] ?? '') == 'internship' ? 'selected' : '' }}>Prácticas</option>
                                    <option value="volunteer" {{ old('employment_type', $experience['employment_type'] ?? '') == 'volunteer' ? 'selected' : '' }}>Voluntario</option>
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
                                       value="{{ old('position', $experience['position'] ?? '') }}"
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
                                       value="{{ old('department', $experience['department'] ?? '') }}"
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
                                       value="{{ old('start_date', $experience['start_date'] ?? '') }}" required>
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
                                       value="{{ old('end_date', $experience['end_date'] ?? '') }}">
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
                                           {{ old('is_current', $experience['is_current'] ?? false) ? 'checked' : '' }}>
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
                                       value="{{ old('location', $experience['location'] ?? '') }}"
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
                                    <option value="on-site" {{ old('location_type', $experience['location_type'] ?? '') == 'on-site' ? 'selected' : '' }}>Presencial</option>
                                    <option value="remote" {{ old('location_type', $experience['location_type'] ?? '') == 'remote' ? 'selected' : '' }}>Remoto</option>
                                    <option value="hybrid" {{ old('location_type', $experience['location_type'] ?? '') == 'hybrid' ? 'selected' : '' }}>Híbrido</option>
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
                                  placeholder="Describe tu rol, responsabilidades principales, el contexto del trabajo...">{{ old('description', $experience['description'] ?? '') }}</textarea>
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
                            @php
                                $responsibilities = old('responsibilities');
                                if (!$responsibilities && isset($experience['responsibilities'])) {
                                    $responsibilities = is_array($experience['responsibilities']) ? $experience['responsibilities'] : json_decode($experience['responsibilities'], true);
                                }
                                $responsibilities = $responsibilities ?: [''];
                            @endphp
                            @foreach($responsibilities as $index => $responsibility)
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
                            @php
                                $achievements = old('achievements');
                                if (!$achievements && isset($experience['achievements'])) {
                                    $achievements = is_array($experience['achievements']) ? $experience['achievements'] : json_decode($experience['achievements'], true);
                                }
                                $achievements = $achievements ?: [''];
                            @endphp
                            @foreach($achievements as $index => $achievement)
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
                            @php
                                $technologies = old('technologies');
                                if (!$technologies && isset($experience['technologies'])) {
                                    $technologies = is_array($experience['technologies']) ? $experience['technologies'] : json_decode($experience['technologies'], true);
                                }
                                $technologies = $technologies ?: [''];
                            @endphp
                            @foreach($technologies as $index => $technology)
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
                                       value="{{ old('company_url', $experience['company_url'] ?? '') }}"
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
                                       value="{{ old('company_logo_url', $experience['company_logo_url'] ?? '') }}"
                                       placeholder="https://www.empresa.com/logo.png">
                                @error('company_logo_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">O sube una nueva imagen abajo</small>
                            </div>
                        </div>
                    </div>

                    @if(!empty($experience['company_logo_url']))
                    <div class="mb-3">
                        <label class="form-label">Logo Actual:</label>
                        <div>
                            <img src="{{ $experience['company_logo_url'] }}"
                                 alt="Logo actual"
                                 class="img-thumbnail"
                                 style="max-width: 150px; max-height: 150px;">
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="company_logo" class="form-label">
                            <i class="bi bi-upload me-1"></i>
                            Cambiar Logo de la Empresa
                        </label>
                        <input type="file" name="company_logo" id="company_logo"
                               class="form-control @error('company_logo') is-invalid @enderror"
                               accept="image/jpeg,image/jpg,image/png,image/webp">
                        @error('company_logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Formatos: JPG, PNG, WebP. Máximo 2MB. Si subes una nueva imagen, reemplazará la actual.</small>
                    </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_featured" id="is_featured"
                                       class="form-check-input" value="1"
                                       {{ old('is_featured', $experience['is_featured'] ?? false) ? 'checked' : '' }}>
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
                                       value="{{ old('order_position', $experience['order_position'] ?? 0) }}" min="0">
                                @error('order_position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.experience.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Volver a la Lista
                            </a>
                            <a href="{{ route('admin.experience.show', $experience['id']) }}" class="btn btn-outline-info ms-2">
                                <i class="bi bi-eye me-2"></i>
                                Ver Detalles
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>
                            Actualizar Experiencia
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
                    Información Actual
                </h6>
                <div class="small text-muted">
                    <div class="mb-2">
                        <strong>Creado:</strong> {{ \Carbon\Carbon::parse($experience['created_at'] ?? now())->format('d/m/Y H:i') }}
                    </div>
                    @if(!empty($experience['updated_at']) && $experience['updated_at'] !== $experience['created_at'])
                        <div class="mb-2">
                            <strong>Última actualización:</strong> {{ \Carbon\Carbon::parse($experience['updated_at'])->format('d/m/Y H:i') }}
                        </div>
                    @endif
                    <div class="mb-2">
                        <strong>ID:</strong> {{ $experience['id'] ?? 'No disponible' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-light mt-3">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-lightbulb me-2"></i>
                    Tips de Edición
                </h6>
                <ul class="small text-muted">
                    <li class="mb-2">
                        Actualiza las fechas si hay cambios
                    </li>
                    <li class="mb-2">
                        Agrega nuevos logros y responsabilidades
                    </li>
                    <li class="mb-2">
                        Actualiza las tecnologías utilizadas
                    </li>
                    <li>
                        Verifica que las URLs sean válidas
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

    // Funcionalidad para arrays dinámicos
    function setupDynamicArray(containerSelector, addButtonSelector, removeButtonClass, itemClass) {
        document.querySelector(addButtonSelector).addEventListener('click', function() {
            const container = document.querySelector(containerSelector);
            const newItem = container.querySelector(`.${itemClass}`).cloneNode(true);
            newItem.querySelector('input').value = '';
            container.appendChild(newItem);

            newItem.querySelector(`.${removeButtonClass}`).addEventListener('click', function() {
                if (container.children.length > 1) {
                    newItem.remove();
                }
            });
        });

        document.querySelectorAll(`.${removeButtonClass}`).forEach(button => {
            button.addEventListener('click', function() {
                const container = document.querySelector(containerSelector);
                if (container.children.length > 1) {
                    button.closest(`.${itemClass}`).remove();
                }
            });
        });
    }

    // Configurar arrays dinámicos
    setupDynamicArray('#responsibilities-container', '#add-responsibility', 'remove-responsibility', 'responsibility-item');
    setupDynamicArray('#achievements-container', '#add-achievement', 'remove-achievement', 'achievement-item');
    setupDynamicArray('#technologies-container', '#add-technology', 'remove-technology', 'technology-item');
});
</script>
@endsection
