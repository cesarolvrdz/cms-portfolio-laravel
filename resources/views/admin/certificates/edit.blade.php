@extends('layouts.admin')

@section('title', 'Editar Certificado')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit text-primary me-2"></i>
                Editar Certificado
            </h1>
            <p class="text-muted">Modifica la información del certificado: {{ $certificate['title'] }}</p>
        </div>
        <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Volver a la lista
        </a>
    </div>

    <form action="{{ route('admin.certificates.update', $certificate['id']) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Información Principal -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Información Principal
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Título del Certificado *</label>
                                <input type="text"
                                       class="form-control @error('title') is-invalid @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title', $certificate['title']) }}"
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="institution" class="form-label">Institución *</label>
                                <input type="text"
                                       class="form-control @error('institution') is-invalid @enderror"
                                       id="institution"
                                       name="institution"
                                       value="{{ old('institution', $certificate['institution']) }}"
                                       required>
                                @error('institution')
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
                                      placeholder="Describe lo que aprendiste o las habilidades desarrolladas...">{{ old('description', $certificate['description']) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Categoría *</label>
                                <select class="form-select @error('category') is-invalid @enderror"
                                        id="category"
                                        name="category"
                                        required>
                                    <option value="">Selecciona una categoría</option>
                                    <option value="programming" {{ old('category', $certificate['category']) == 'programming' ? 'selected' : '' }}>Programación</option>
                                    <option value="design" {{ old('category', $certificate['category']) == 'design' ? 'selected' : '' }}>Diseño</option>
                                    <option value="marketing" {{ old('category', $certificate['category']) == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="data" {{ old('category', $certificate['category']) == 'data' ? 'selected' : '' }}>Datos y Analytics</option>
                                    <option value="cloud" {{ old('category', $certificate['category']) == 'cloud' ? 'selected' : '' }}>Cloud Computing</option>
                                    <option value="security" {{ old('category', $certificate['category']) == 'security' ? 'selected' : '' }}>Seguridad</option>
                                    <option value="management" {{ old('category', $certificate['category']) == 'management' ? 'selected' : '' }}>Gestión de Proyectos</option>
                                    <option value="language" {{ old('category', $certificate['category']) == 'language' ? 'selected' : '' }}>Idiomas</option>
                                    <option value="other" {{ old('category', $certificate['category']) == 'other' ? 'selected' : '' }}>Otros</option>
                                    @foreach($categories as $category)
                                        @if(!in_array($category, ['programming', 'design', 'marketing', 'data', 'cloud', 'security', 'management', 'language', 'other']))
                                            <option value="{{ $category }}" {{ old('category', $certificate['category']) == $category ? 'selected' : '' }}>
                                                {{ ucfirst($category) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="custom_category" class="form-label">Categoría Personalizada</label>
                                <input type="text"
                                       class="form-control"
                                       id="custom_category"
                                       placeholder="Escribe una nueva categoría...">
                                <div class="form-text">Se usará si no seleccionas una categoría existente</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="skills" class="form-label">Habilidades/Tecnologías</label>
                            <input type="text"
                                   class="form-control @error('skills') is-invalid @enderror"
                                   id="skills"
                                   name="skills"
                                   value="{{ old('skills', is_array($certificate['skills']) ? implode(', ', $certificate['skills']) : '') }}"
                                   placeholder="React, Node.js, MongoDB, etc. (separadas por comas)">
                            <div class="form-text">Lista las principales habilidades o tecnologías del certificado, separadas por comas</div>
                            @error('skills')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Fechas y Detalles -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar me-2"></i>
                            Fechas y Detalles
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="issue_date" class="form-label">Fecha de Emisión *</label>
                                <input type="date"
                                       class="form-control @error('issue_date') is-invalid @enderror"
                                       id="issue_date"
                                       name="issue_date"
                                       value="{{ old('issue_date', $certificate['issue_date']) }}"
                                       required>
                                @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="expiry_date" class="form-label">Fecha de Expiración</label>
                                <input type="date"
                                       class="form-control @error('expiry_date') is-invalid @enderror"
                                       id="expiry_date"
                                       name="expiry_date"
                                       value="{{ old('expiry_date', $certificate['expiry_date']) }}">
                                <div class="form-text">Déjalo vacío si el certificado no expira</div>
                                @error('expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="credential_id" class="form-label">ID del Certificado</label>
                                <input type="text"
                                       class="form-control @error('credential_id') is-invalid @enderror"
                                       id="credential_id"
                                       name="credential_id"
                                       value="{{ old('credential_id', $certificate['credential_id']) }}"
                                       placeholder="Ej: ABC123456">
                                <div class="form-text">ID único proporcionado por la institución</div>
                                @error('credential_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="credential_url" class="form-label">URL de Verificación</label>
                                <input type="url"
                                       class="form-control @error('credential_url') is-invalid @enderror"
                                       id="credential_url"
                                       name="credential_url"
                                       value="{{ old('credential_url', $certificate['credential_url']) }}"
                                       placeholder="https://...">
                                <div class="form-text">Enlace para verificar el certificado online</div>
                                @error('credential_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Lateral -->
            <div class="col-lg-4">
                <!-- Archivos Actuales -->
                @if($certificate['image_url'] || $certificate['pdf_url'])
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            Archivos Actuales
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($certificate['image_url'])
                            <div class="mb-3">
                                <label class="form-label">Imagen Actual</label>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $certificate['image_url'] }}"
                                         alt="{{ $certificate['title'] }}"
                                         class="img-thumbnail me-3"
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="remove_image"
                                               name="remove_image"
                                               value="1">
                                        <label class="form-check-label text-danger" for="remove_image">
                                            <i class="fas fa-trash me-1"></i>
                                            Eliminar imagen
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($certificate['pdf_url'])
                            <div class="mb-3">
                                <label class="form-label">PDF Actual</label>
                                <div class="d-flex align-items-center">
                                    <a href="{{ $certificate['pdf_url'] }}"
                                       target="_blank"
                                       class="btn btn-outline-primary btn-sm me-3">
                                        <i class="fas fa-file-pdf me-1"></i>
                                        Ver PDF
                                    </a>
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="remove_pdf"
                                               name="remove_pdf"
                                               value="1">
                                        <label class="form-check-label text-danger" for="remove_pdf">
                                            <i class="fas fa-trash me-1"></i>
                                            Eliminar PDF
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Nuevos Archivos -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-upload me-2"></i>
                            Subir Nuevos Archivos
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Nueva imagen -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Nueva Imagen Representativa</label>
                            <input type="file"
                                   class="form-control @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/*">
                            <div class="form-text">Reemplazará la imagen actual (máx. 2MB)</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Preview de nueva imagen -->
                            <div id="image-preview" class="mt-2" style="display: none;">
                                <img id="preview-img" class="img-thumbnail" style="max-width: 100%; max-height: 200px;">
                            </div>
                        </div>

                        <!-- Nuevo PDF -->
                        <div class="mb-3">
                            <label for="pdf" class="form-label">Nuevo Certificado PDF</label>
                            <input type="file"
                                   class="form-control @error('pdf') is-invalid @enderror"
                                   id="pdf"
                                   name="pdf"
                                   accept=".pdf">
                            <div class="form-text">Reemplazará el PDF actual (máx. 10MB)</div>
                            @error('pdf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                        <div class="mb-3">
                            <label for="order_position" class="form-label">Orden de Visualización</label>
                            <input type="number"
                                   class="form-control @error('order_position') is-invalid @enderror"
                                   id="order_position"
                                   name="order_position"
                                   value="{{ old('order_position', $certificate['order_position'] ?? 0) }}"
                                   min="0">
                            <div class="form-text">0 = primero, mayor número = después</div>
                            @error('order_position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="is_featured"
                                   name="is_featured"
                                   value="1"
                                   {{ old('is_featured', $certificate['is_featured']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                <i class="fas fa-star text-warning me-1"></i>
                                Destacar certificado
                            </label>
                            <div class="form-text">Se mostrará prominentemente en el portafolio</div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $certificate['is_active']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <i class="fas fa-eye text-success me-1"></i>
                                Visible en el portafolio
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Actualizar Certificado
                            </button>
                            <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
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
    // Preview de nueva imagen
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });

    // Manejar categoría personalizada
    const categorySelect = document.getElementById('category');
    const customCategoryInput = document.getElementById('custom_category');

    customCategoryInput.addEventListener('input', function() {
        if (this.value.trim()) {
            categorySelect.value = '';
            const customValue = this.value.trim().toLowerCase();
            let existingOption = categorySelect.querySelector(`option[value="${customValue}"]`);

            if (!existingOption) {
                const newOption = document.createElement('option');
                newOption.value = customValue;
                newOption.textContent = this.value.trim();
                newOption.selected = true;
                categorySelect.appendChild(newOption);
            } else {
                existingOption.selected = true;
            }
        }
    });

    categorySelect.addEventListener('change', function() {
        if (this.value) {
            customCategoryInput.value = '';
        }
    });

    // Validación de archivos
    const imageInput = document.getElementById('image');
    const pdfInput = document.getElementById('pdf');

    function validateFileSize(input, maxSizeMB, fileType) {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const maxSizeBytes = maxSizeMB * 1024 * 1024;
                if (file.size > maxSizeBytes) {
                    alert(`El archivo ${fileType} es muy grande. Máximo permitido: ${maxSizeMB}MB. Tamaño actual: ${(file.size / 1024 / 1024).toFixed(2)}MB`);
                    this.value = '';
                    return;
                }
            }
        });
    }

    // Aplicar validación a los inputs de archivo
    if (imageInput) validateFileSize(imageInput, 2, 'de imagen');
    if (pdfInput) validateFileSize(pdfInput, 10, 'PDF');

    // Validación del formulario
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const institution = document.getElementById('institution').value.trim();
        const issueDate = document.getElementById('issue_date').value;
        const category = document.getElementById('category').value;
        const customCategory = document.getElementById('custom_category').value.trim();

        if (!title || !institution || !issueDate) {
            e.preventDefault();
            alert('Por favor completa todos los campos obligatorios.');
            return;
        }

        if (!category && !customCategory) {
            e.preventDefault();
            alert('Por favor selecciona una categoría o escribe una personalizada.');
            return;
        }

        // Validar tamaños de archivo antes del envío
        const imageFile = imageInput.files[0];
        const pdfFile = pdfInput.files[0];

        if (imageFile && imageFile.size > 2 * 1024 * 1024) {
            e.preventDefault();
            alert('La imagen es muy grande (máx. 2MB)');
            return;
        }

        if (pdfFile && pdfFile.size > 10 * 1024 * 1024) {
            e.preventDefault();
            alert('El PDF es muy grande (máx. 10MB)');
            return;
        }

        // Si hay categoría personalizada, asignarla al select
        if (customCategory) {
            document.getElementById('category').value = customCategory.toLowerCase();
        }
    });
});
</script>
@endpush
@endsection
