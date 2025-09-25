@extends('layouts.admin')

@section('title', 'Editar Proyecto')
@section('page-title', 'Editar Proyecto')
@section('page-description', 'Modifica la información de tu proyecto')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    Editar: {{ $project['title'] }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.projects.update', $project['id']) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    <i class="bi bi-type me-1"></i>
                                    Título del Proyecto
                                </label>
                                <input type="text" name="title" id="title" class="form-control"
                                       value="{{ $project['title'] }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">
                                    <i class="bi bi-flag me-1"></i>
                                    Estado
                                </label>
                                <select name="status" id="status" class="form-select">
                                    <option value="completed" @if($project['status']==='completed') selected @endif>Completado</option>
                                    <option value="in-progress" @if($project['status']==='in-progress') selected @endif>En Progreso</option>
                                    <option value="planned" @if($project['status']==='planned') selected @endif>Planeado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="bi bi-file-text me-1"></i>
                            Descripción
                        </label>
                        <textarea name="description" id="description" class="form-control" rows="4" required>{{ $project['description'] }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="link" class="form-label">
                                    <i class="bi bi-link me-1"></i>
                                    URL del Proyecto (Opcional)
                                </label>
                                <input type="url" name="link" id="link" class="form-control"
                                       value="{{ $project['link'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tech" class="form-label">
                                    <i class="bi bi-code-slash me-1"></i>
                                    Tecnologías (separadas por coma)
                                </label>
                                <input type="text" name="tech" id="tech" class="form-control"
                                       value="{{ is_array($project['tech']) ? implode(', ', $project['tech']) : '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label">
                            <i class="bi bi-image me-1"></i>
                            Imagen del Proyecto
                        </label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        @if(!empty($project['image']))
                            <div class="mt-2">
                                <small class="text-muted">Imagen actual:</small><br>
                                <img src="{{ $project['image'] }}" alt="Imagen actual"
                                     class="img-thumbnail" style="max-width:200px;">
                            </div>
                        @endif
                        <small class="form-text text-muted">Deja vacío si no quieres cambiar la imagen</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>
                            Actualizar Proyecto
                        </button>
                        <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Volver
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
                    <i class="bi bi-eye me-2"></i>
                    Vista Previa
                </h6>
            </div>
            <div class="card-body">
                @if(!empty($project['image']))
                    <img src="{{ $project['image'] }}" alt="Preview" class="img-fluid rounded mb-3">
                @endif
                <h6>{{ $project['title'] }}</h6>
                <p class="text-muted small mb-2">{{ Str::limit($project['description'], 100) }}</p>
                @if(!empty($project['tech']) && is_array($project['tech']))
                    <div class="mb-2">
                        @foreach($project['tech'] as $tech)
                            <span class="badge bg-light text-dark me-1">{{ $tech }}</span>
                        @endforeach
                    </div>
                @endif
                <span class="badge bg-success">{{ ucfirst($project['status']) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
