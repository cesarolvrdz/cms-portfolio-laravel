@extends('layouts.admin')

@section('title', 'Nuevo Proyecto')
@section('page-title', 'Crear Nuevo Proyecto')
@section('page-description', 'Agrega un nuevo proyecto a tu portafolio')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    Información del Proyecto
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    <i class="bi bi-type me-1"></i>
                                    Título del Proyecto
                                </label>
                                <input type="text" name="title" id="title" class="form-control"
                                       placeholder="Ej: Mi Portfolio Website" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">
                                    <i class="bi bi-flag me-1"></i>
                                    Estado
                                </label>
                                <select name="status" id="status" class="form-select">
                                    <option value="completed">Completado</option>
                                    <option value="in-progress">En Progreso</option>
                                    <option value="planned">Planeado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="bi bi-file-text me-1"></i>
                            Descripción
                        </label>
                        <textarea name="description" id="description" class="form-control" rows="4"
                                  placeholder="Describe tu proyecto, las tecnologías usadas y los desafíos superados..." required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="link" class="form-label">
                                    <i class="bi bi-link me-1"></i>
                                    URL del Proyecto (Opcional)
                                </label>
                                <input type="url" name="link" id="link" class="form-control"
                                       placeholder="https://tu-proyecto.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tech" class="form-label">
                                    <i class="bi bi-code-slash me-1"></i>
                                    Tecnologías (separadas por coma)
                                </label>
                                <input type="text" name="tech" id="tech" class="form-control"
                                       placeholder="Laravel, Vue.js, Supabase">
                                <small class="form-text text-muted">Ej: Laravel, Vue.js, Tailwind CSS</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label">
                            <i class="bi bi-image me-1"></i>
                            Imagen del Proyecto
                        </label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Formatos soportados: JPG, PNG, WebP (máx. 2MB)</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>
                            Guardar Proyecto
                        </button>
                        <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Consejos
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        Usa títulos descriptivos y atractivos
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        Describe el problema que resuelve tu proyecto
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        Incluye las tecnologías principales
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check text-success me-2"></i>
                        Sube una imagen representativa
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
