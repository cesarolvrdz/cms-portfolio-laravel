@extends('layouts.admin')

@section('title', 'Nuevo Usuario')
@section('page-title', 'Crear Nuevo Usuario')
@section('page-description', 'Agrega un nuevo usuario al sistema de gestión')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-plus me-2"></i>
                    Información del Usuario
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person me-1"></i>
                                    Nombre Completo <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
                                       placeholder="Ej: Juan Pérez García" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="role" class="form-label">
                                    <i class="bi bi-shield me-1"></i>
                                    Rol <span class="text-danger">*</span>
                                </label>
                                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">Seleccionar rol</option>
                                    <option value="viewer" {{ old('role') == 'viewer' ? 'selected' : '' }}>Visualizador</option>
                                    <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>Editor</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                    <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-1"></i>
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="email" id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="usuario@ejemplo.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-1"></i>
                                    Contraseña <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Mínimo 8 caracteres" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="bi bi-lock-fill me-1"></i>
                                    Confirmar Contraseña <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control"
                                       placeholder="Repetir contraseña" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="avatar_url" class="form-label">
                            <i class="bi bi-image me-1"></i>
                            URL del Avatar
                        </label>
                        <input type="url" name="avatar_url" id="avatar_url"
                               class="form-control @error('avatar_url') is-invalid @enderror"
                               value="{{ old('avatar_url') }}"
                               placeholder="https://ejemplo.com/avatar.jpg">
                        @error('avatar_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_active" id="is_active"
                                       class="form-check-input" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Usuario activo
                                </label>
                                <small class="form-text text-muted d-block">
                                    Los usuarios inactivos no pueden acceder al sistema
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" name="two_factor_enabled" id="two_factor_enabled"
                                       class="form-check-input" value="1"
                                       {{ old('two_factor_enabled') ? 'checked' : '' }}>
                                <label for="two_factor_enabled" class="form-check-label">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Autenticación de dos factores
                                </label>
                                <small class="form-text text-muted d-block">
                                    Recomendado para roles administrativos
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>
                            Crear Usuario
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
                    Información sobre Roles
                </h6>
                <div class="mb-3">
                    <span class="badge bg-danger">Super Admin</span>
                    <small class="d-block text-muted">
                        Acceso total al sistema, incluyendo gestión de usuarios y configuraciones críticas.
                    </small>
                </div>
                <div class="mb-3">
                    <span class="badge bg-primary">Administrador</span>
                    <small class="d-block text-muted">
                        Gestión completa del contenido (proyectos, perfil, educación, experiencia).
                    </small>
                </div>
                <div class="mb-3">
                    <span class="badge bg-info">Editor</span>
                    <small class="d-block text-muted">
                        Puede editar el contenido existente pero no crear/eliminar elementos.
                    </small>
                </div>
                <div class="mb-3">
                    <span class="badge bg-secondary">Visualizador</span>
                    <small class="d-block text-muted">
                        Solo puede ver el contenido, sin permisos de edición.
                    </small>
                </div>
            </div>
        </div>

        <div class="card bg-light mt-3">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-shield-exclamation me-2"></i>
                    Recomendaciones de Seguridad
                </h6>
                <ul class="small text-muted">
                    <li class="mb-2">
                        Use contraseñas de al menos 8 caracteres
                    </li>
                    <li class="mb-2">
                        Combine letras, números y símbolos
                    </li>
                    <li class="mb-2">
                        Active 2FA para roles administrativos
                    </li>
                    <li class="mb-2">
                        Revise periódicamente los accesos
                    </li>
                    <li>
                        Desactive usuarios que ya no necesiten acceso
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const twoFactorCheckbox = document.getElementById('two_factor_enabled');

    roleSelect.addEventListener('change', function() {
        // Sugerir 2FA para roles administrativos
        if (['admin', 'super_admin'].includes(this.value)) {
            twoFactorCheckbox.checked = true;
        }
    });

    // Mostrar fuerza de la contraseña
    const passwordInput = document.getElementById('password');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;

        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        // Remover indicadores previos
        const existingIndicator = this.parentNode.querySelector('.password-strength');
        if (existingIndicator) {
            existingIndicator.remove();
        }

        if (password.length > 0) {
            const indicator = document.createElement('small');
            indicator.className = 'password-strength form-text';

            if (strength < 3) {
                indicator.className += ' text-danger';
                indicator.textContent = 'Contraseña débil';
            } else if (strength < 4) {
                indicator.className += ' text-warning';
                indicator.textContent = 'Contraseña media';
            } else {
                indicator.className += ' text-success';
                indicator.textContent = 'Contraseña fuerte';
            }

            this.parentNode.appendChild(indicator);
        }
    });
});
</script>
@endsection
