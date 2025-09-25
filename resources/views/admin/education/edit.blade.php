@extends('layouts.admin')

@section('title', 'Editar Formación Académica')
@section('page-title', 'Editar Formación Académica')
@section('page-description', 'Actualiza la información de tu formación')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    Editar Información de la Formación
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.education.update', $education['id']) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="institution" class="form-label">
                                    <i class="bi bi-building me-1"></i>
                                    Institución <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="institution" id="institution"
                                       class="form-control @error('institution') is-invalid @enderror"
                                       value="{{ old('institution', $education['institution'] ?? '') }}"
                                       placeholder="Ej: Universidad Nacional, Platzi, Coursera" required>
                                @error('institution')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="type" class="form-label">
                                    <i class="bi bi-tag me-1"></i>
                                    Tipo
                                </label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                                    <option value="degree" {{ old('type', $education['type'] ?? '') == 'degree' ? 'selected' : '' }}>Título Universitario</option>
                                    <option value="certification" {{ old('type', $education['type'] ?? '') == 'certification' ? 'selected' : '' }}>Certificación</option>
                                    <option value="course" {{ old('type', $education['type'] ?? '') == 'course' ? 'selected' : '' }}>Curso</option>
                                    <option value="bootcamp" {{ old('type', $education['type'] ?? '') == 'bootcamp' ? 'selected' : '' }}>Bootcamp</option>
                                    <option value="workshop" {{ old('type', $education['type'] ?? '') == 'workshop' ? 'selected' : '' }}>Taller</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="degree" class="form-label">
                            <i class="bi bi-mortarboard me-1"></i>
                            Título/Certificación <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="degree" id="degree"
                               class="form-control @error('degree') is-invalid @enderror"
                               value="{{ old('degree', $education['degree'] ?? '') }}"
                               placeholder="Ej: Licenciatura en Ingeniería de Sistemas, Curso de React Avanzado" required>
                        @error('degree')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="field_of_study" class="form-label">
                            <i class="bi bi-book me-1"></i>
                            Campo de Estudio
                        </label>
                        <input type="text" name="field_of_study" id="field_of_study"
                               class="form-control @error('field_of_study') is-invalid @enderror"
                               value="{{ old('field_of_study', $education['field_of_study'] ?? '') }}"
                               placeholder="Ej: Ingeniería de Software, Desarrollo Frontend, Ciencias de Datos">
                        @error('field_of_study')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">
                                    <i class="bi bi-calendar me-1"></i>
                                    Fecha de Inicio
                                </label>
                                <input type="date" name="start_date" id="start_date"
                                       class="form-control @error('start_date') is-invalid @enderror"
                                       value="{{ old('start_date', $education['start_date'] ?? '') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    Fecha de Finalización
                                </label>
                                <input type="date" name="end_date" id="end_date"
                                       class="form-control @error('end_date') is-invalid @enderror"
                                       value="{{ old('end_date', $education['end_date'] ?? '') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="form-check">
                                    <input type="checkbox" name="is_current" id="is_current"
                                           class="form-check-input" value="1"
                                           {{ old('is_current', $education['is_current'] ?? false) ? 'checked' : '' }}>
                                    <label for="is_current" class="form-check-label">
                                        En curso
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    Ubicación
                                </label>
                                <input type="text" name="location" id="location"
                                       class="form-control @error('location') is-invalid @enderror"
                                       value="{{ old('location', $education['location'] ?? '') }}"
                                       placeholder="Ej: Ciudad de México, México | Online">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="grade" class="form-label">
                                    <i class="bi bi-award me-1"></i>
                                    Calificación/Nota
                                </label>
                                <input type="text" name="grade" id="grade"
                                       class="form-control @error('grade') is-invalid @enderror"
                                       value="{{ old('grade', $education['grade'] ?? '') }}"
                                       placeholder="Ej: 9.5/10, Magna Cum Laude, Aprobado">
                                @error('grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="bi bi-file-text me-1"></i>
                            Descripción
                        </label>
                        <textarea name="description" id="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Describe los conocimientos adquiridos, proyectos realizados, materias destacadas...">{{ old('description', $education['description'] ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="institution_url" class="form-label">
                                    <i class="bi bi-link me-1"></i>
                                    URL de la Institución
                                </label>
                                <input type="url" name="institution_url" id="institution_url"
                                       class="form-control @error('institution_url') is-invalid @enderror"
                                       value="{{ old('institution_url', $education['institution_url'] ?? '') }}"
                                       placeholder="https://www.universidad.edu">
                                @error('institution_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="certificate_url" class="form-label">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>
                                    URL del Certificado
                                </label>
                                <input type="url" name="certificate_url" id="certificate_url"
                                       class="form-control @error('certificate_url') is-invalid @enderror"
                                       value="{{ old('certificate_url', $education['certificate_url'] ?? '') }}"
                                       placeholder="https://certificados.com/mi-certificado.pdf">
                                @error('certificate_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_featured" id="is_featured"
                                       class="form-check-input" value="1"
                                       {{ old('is_featured', $education['is_featured'] ?? false) ? 'checked' : '' }}>
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
                                       value="{{ old('order_position', $education['order_position'] ?? 0) }}" min="0">
                                @error('order_position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.education.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Volver a la Lista
                            </a>
                            <a href="{{ route('admin.education.show', $education['id']) }}" class="btn btn-outline-info ms-2">
                                <i class="bi bi-eye me-2"></i>
                                Ver Detalles
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>
                            Actualizar Formación
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
                        <strong>Creado:</strong> {{ \Carbon\Carbon::parse($education['created_at'] ?? now())->format('d/m/Y H:i') }}
                    </div>
                    @if(!empty($education['updated_at']) && $education['updated_at'] !== $education['created_at'])
                        <div class="mb-2">
                            <strong>Última actualización:</strong> {{ \Carbon\Carbon::parse($education['updated_at'])->format('d/m/Y H:i') }}
                        </div>
                    @endif
                    <div class="mb-2">
                        <strong>ID:</strong> {{ $education['id'] ?? 'No disponible' }}
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
                        Verifica que las fechas sean correctas
                    </li>
                    <li class="mb-2">
                        Si sigues estudiando, marca "En curso"
                    </li>
                    <li class="mb-2">
                        Actualiza las URLs si han cambiado
                    </li>
                    <li>
                        Ajusta el orden para organizar tu portafolio
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endsection
