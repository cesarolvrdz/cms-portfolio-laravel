@extends('layouts.admin')

@section('title', 'Editar Usuario')
@section('page-title', 'Editar Usuario')
@section('page-description', 'Actualiza la información del usuario')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    Editar Información del Usuario
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user['id']) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person me-1"></i>
                                    Nombre Completo <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user['name'] ?? '') }}"
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
                                    <option value="viewer" {{ old('role', $user['role'] ?? '') == 'viewer' ? 'selected' : '' }}>Visualizador</option>
                                    <option value="editor" {{ old('role', $user['role'] ?? '') == 'editor' ? 'selected' : '' }}>Editor</option>
                                    <option value="admin" {{ old('role', $user['role'] ?? '') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                    <option value="super_admin" {{ old('role', $user['role'] ?? '') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
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
                               value="{{ old('email', $user['email'] ?? '') }}"
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
                                    Nueva Contraseña
                                </label>
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Dejar vacío para mantener la actual">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Solo complete si desea cambiar la contraseña
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="bi bi-lock-fill me-1"></i>
                                    Confirmar Nueva Contraseña
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control"
                                       placeholder="Repetir nueva contraseña">
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
                               value="{{ old('avatar_url', $user['avatar_url'] ?? '') }}"
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
                                       {{ old('is_active', $user['is_active'] ?? true) ? 'checked' : '' }}>
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
                                       {{ old('two_factor_enabled', $user['two_factor_enabled'] ?? false) ? 'checked' : '' }}>
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
                        <div>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Volver a la Lista
                            </a>
                            <a href="{{ route('admin.users.show', $user['id']) }}" class="btn btn-outline-info ms-2">
                                <i class="bi bi-eye me-2"></i>
                                Ver Detalles
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>
                            Actualizar Usuario
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
                        <strong>Creado:</strong> {{ \Carbon\Carbon::parse($user['created_at'] ?? now())->format('d/m/Y H:i') }}
                    </div>
                    @if(!empty($user['updated_at']) && $user['updated_at'] !== $user['created_at'])
                        <div class="mb-2">
                            <strong>Última actualización:</strong> {{ \Carbon\Carbon::parse($user['updated_at'])->format('d/m/Y H:i') }}
                        </div>
                    @endif
                    @if(!empty($user['last_login']))
                        <div class="mb-2">
                            <strong>Último acceso:</strong> {{ \Carbon\Carbon::parse($user['last_login'])->format('d/m/Y H:i') }}
                        </div>
                    @endif
                    @if(!empty($user['email_verified_at']))
                        <div class="mb-2">
                            <strong>Email verificado:</strong> {{ \Carbon\Carbon::parse($user['email_verified_at'])->format('d/m/Y H:i') }}
                        </div>
                    @else
                        <div class="mb-2">
                            <strong>Email:</strong> <span class="text-warning">Sin verificar</span>
                        </div>
                    @endif
                    <div class="mb-2">
                        <strong>ID:</strong> {{ $user['id'] ?? 'No disponible' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-light mt-3">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Precauciones
                </h6>
                <ul class="small text-muted">
                    @if(($user['role'] ?? '') === 'super_admin')
                        <li class="mb-2 text-danger">
                            <strong>Super Admin:</strong> Tenga cuidado al modificar este usuario.
                        </li>
                    @endif
                    <li class="mb-2">
                        Al cambiar el email se deberá verificar nuevamente
                    </li>
                    <li class="mb-2">
                        La contraseña se encripta automáticamente
                    </li>
                    <li class="mb-2">
                        Los usuarios inactivos pierden acceso inmediatamente
                    </li>
                    <li>
                        Cambios de rol afectan los permisos del usuario
                    </li>
                </ul>
            </div>
        </div>

        @if(($user['role'] ?? '') === 'super_admin')
            <div class="card border-warning mt-3">
                <div class="card-body">
                    <h6 class="card-title text-warning">
                        <i class="bi bi-shield-exclamation me-2"></i>
                        Usuario Super Administrador
                    </h6>
                    <p class="small text-muted">
                        Este usuario tiene acceso total al sistema. Los cambios deben realizarse con extrema precaución.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const twoFactorCheckbox = document.getElementById('two_factor_enabled');

    roleSelect.addEventListener('change', function() {
        // Sugerir 2FA para roles administrativos
        if (['admin', 'super_admin'].includes(this.value)) {
            if (!twoFactorCheckbox.checked) {
                // Mostrar sugerencia visual
                twoFactorCheckbox.parentNode.classList.add('bg-warning', 'p-2', 'rounded');
                setTimeout(() => {
                    twoFactorCheckbox.parentNode.classList.remove('bg-warning', 'p-2', 'rounded');
                }, 3000);
            }
        }
    });

    // Mostrar fuerza de la contraseña si se está cambiando
    const passwordInput = document.getElementById('password');

    passwordInput.addEventListener('input', function() {
        const password = this.value;

        // Remover indicadores previos
        const existingIndicator = this.parentNode.querySelector('.password-strength');
        if (existingIndicator) {
            existingIndicator.remove();
        }

        if (password.length > 0) {
            let strength = 0;

            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

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
