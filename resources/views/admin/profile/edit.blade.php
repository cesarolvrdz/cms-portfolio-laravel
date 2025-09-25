@extends('layouts.admin')

@section('title', 'Editar Perfil')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit text-primary me-2"></i>Editar Perfil
            </h1>
            <p class="text-muted">Actualiza tu información personal</p>
        </div>
        <a href="{{ route('admin.profile.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información Personal</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Avatar Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">Imagen de Perfil</label>
                                <div class="d-flex align-items-start gap-3">
                                    <!-- Avatar actual -->
                                    <div class="text-center">
                                        @if(isset($profile['avatar_url']) && $profile['avatar_url'])
                                            <img src="{{ $profile['avatar_url'] }}" alt="Avatar actual"
                                                 class="rounded-circle mb-2" width="80" height="80"
                                                 style="object-fit: cover; border: 3px solid #e9ecef;">
                                            <div class="small text-muted">Avatar actual</div>
                                        @else
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-2"
                                                 style="width: 80px; height: 80px; border: 3px solid #e9ecef;">
                                                <i class="bi bi-person-circle text-muted" style="font-size: 2rem;"></i>
                                            </div>
                                            <div class="small text-muted">Sin avatar</div>
                                        @endif
                                    </div>

                                    <!-- Upload field -->
                                    <div class="flex-grow-1">
                                        <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                               id="avatar" name="avatar" accept="image/*">
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño máximo: 2MB.
                                        </div>
                                        @error('avatar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name"
                                       value="{{ old('name', $profile['name'] ?? '') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Título Profesional *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="title" name="title"
                                       value="{{ old('title', $profile['title'] ?? '') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email"
                                       value="{{ old('email', $profile['email'] ?? '') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Ubicación</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                       id="location" name="location"
                                       value="{{ old('location', $profile['location'] ?? '') }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone"
                                       value="{{ old('phone', $profile['phone'] ?? '') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="resume_url" class="form-label">URL del CV</label>
                                <input type="url" class="form-control @error('resume_url') is-invalid @enderror"
                                       id="resume_url" name="resume_url"
                                       value="{{ old('resume_url', $profile['resume_url'] ?? '') }}">
                                @error('resume_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Biografía *</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror"
                                      id="bio" name="bio" rows="4" required>{{ old('bio', $profile['bio'] ?? '') }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="skills" class="form-label">Habilidades</label>
                            <input type="text" class="form-control" id="skills-input"
                                   placeholder="Escribe una habilidad y presiona Enter">
                            <small class="text-muted">Presiona Enter para agregar cada habilidad</small>
                            <div id="skills-container" class="mt-2">
                                @php
                                    $skills = [];
                                    if(isset($profile['skills'])) {
                                        $skills = is_string($profile['skills']) ? json_decode($profile['skills'], true) : $profile['skills'];
                                        if(!is_array($skills)) $skills = [];
                                    }
                                @endphp
                                @foreach($skills as $skill)
                                    <span class="badge bg-primary me-1 mb-2">
                                        {{ $skill }}
                                        <button type="button" class="btn-close btn-close-white btn-sm ms-1" data-skill="{{ $skill }}"></button>
                                        <input type="hidden" name="skills[]" value="{{ $skill }}">
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.profile.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const skillsInput = document.getElementById('skills-input');
    const skillsContainer = document.getElementById('skills-container');

    skillsInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const skill = this.value.trim();
            if (skill && !document.querySelector(`[data-skill="${skill}"]`)) {
                addSkill(skill);
                this.value = '';
            }
        }
    });

    function addSkill(skill) {
        const badge = document.createElement('span');
        badge.className = 'badge bg-primary me-1 mb-2';
        badge.innerHTML = `
            ${skill}
            <button type="button" class="btn-close btn-close-white btn-sm ms-1" data-skill="${skill}"></button>
            <input type="hidden" name="skills[]" value="${skill}">
        `;
        skillsContainer.appendChild(badge);

        badge.querySelector('.btn-close').addEventListener('click', function() {
            badge.remove();
        });
    }

    // Event delegation for existing skills
    skillsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-close')) {
            e.target.closest('.badge').remove();
        }
    });

    // Preview de imagen
    const avatarInput = document.getElementById('avatar');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Buscar la imagen actual o crear una nueva
                    const currentImg = document.querySelector('.rounded-circle img');
                    const placeholder = document.querySelector('.rounded-circle .bg-light');

                    if (currentImg) {
                        currentImg.src = e.target.result;
                    } else if (placeholder) {
                        // Reemplazar el placeholder con la nueva imagen
                        const newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.alt = 'Vista previa del avatar';
                        newImg.className = 'rounded-circle mb-2';
                        newImg.width = 80;
                        newImg.height = 80;
                        newImg.style.objectFit = 'cover';
                        newImg.style.border = '3px solid #e9ecef';

                        placeholder.parentNode.replaceChild(newImg, placeholder);

                        // Actualizar el texto
                        const textDiv = document.querySelector('.small.text-muted');
                        if (textDiv) {
                            textDiv.textContent = 'Vista previa';
                        }
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // Validación de archivo
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar tamaño (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('El archivo es demasiado grande. El tamaño máximo permitido es de 2MB.');
                    e.target.value = '';
                    return;
                }

                // Validar tipo
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Formato de archivo no válido. Solo se permiten archivos JPG, PNG, GIF y WebP.');
                    e.target.value = '';
                    return;
                }
            }
        });
    }
});
</script>
@endsection
