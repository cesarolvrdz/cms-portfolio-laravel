@extends('layouts.admin')

@section('title', 'Detalles del Usuario')
@section('page-title', 'Detalles del Usuario')
@section('page-description', 'Información completa del usuario del sistema')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    @if(!empty($user['avatar_url']))
                        <img src="{{ $user['avatar_url'] }}"
                             alt="Avatar"
                             class="rounded-circle me-3"
                             style="width: 60px; height: 60px; object-fit: cover;">
                    @else
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width: 60px; height: 60px;">
                            <i class="bi bi-person text-white fs-4"></i>
                        </div>
                    @endif
                    <div>
                        <h5 class="mb-0">{{ $user['name'] ?? 'Sin nombre' }}</h5>
                        <small class="text-muted">{{ $user['email'] ?? 'Sin email' }}</small>
                    </div>
                </div>
                <div>
                    @php
                        $roleLabels = [
                            'super_admin' => ['text' => 'Super Admin', 'class' => 'danger'],
                            'admin' => ['text' => 'Administrador', 'class' => 'primary'],
                            'editor' => ['text' => 'Editor', 'class' => 'info'],
                            'viewer' => ['text' => 'Visualizador', 'class' => 'secondary']
                        ];
                        $role = $user['role'] ?? 'viewer';
                        $roleInfo = $roleLabels[$role] ?? $roleLabels['viewer'];
                    @endphp
                    <span class="badge bg-{{ $roleInfo['class'] }} fs-6">{{ $roleInfo['text'] }}</span>
                    @if($user['is_active'] ?? true)
                        <span class="badge bg-success ms-1">Activo</span>
                    @else
                        <span class="badge bg-secondary ms-1">Inactivo</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Estado del Email:</strong>
                        <br>
                        @if(!empty($user['email_verified_at']))
                            <span class="text-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Verificado el {{ \Carbon\Carbon::parse($user['email_verified_at'])->format('d/m/Y H:i') }}
                            </span>
                        @else
                            <span class="text-warning">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                Sin verificar
                            </span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Autenticación de Dos Factores:</strong>
                        <br>
                        @if($user['two_factor_enabled'] ?? false)
                            <span class="text-success">
                                <i class="bi bi-shield-check me-1"></i>
                                Habilitado
                            </span>
                        @else
                            <span class="text-danger">
                                <i class="bi bi-shield-x me-1"></i>
                                Deshabilitado
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Último Acceso:</strong>
                        <br>
                        @if(!empty($user['last_login']))
                            @php
                                $lastLogin = \Carbon\Carbon::parse($user['last_login']);
                            @endphp
                            <span class="text-muted">
                                {{ $lastLogin->diffForHumans() }}
                                <br>
                                <small>{{ $lastLogin->format('d/m/Y H:i:s') }}</small>
                            </span>
                        @else
                            <span class="text-muted">Nunca ha iniciado sesión</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Estado de la Cuenta:</strong>
                        <br>
                        @if($user['is_active'] ?? true)
                            <span class="text-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Cuenta activa y funcional
                            </span>
                        @else
                            <span class="text-secondary">
                                <i class="bi bi-pause-circle me-1"></i>
                                Cuenta desactivada
                            </span>
                        @endif
                    </div>
                </div>

                @if(!empty($user['preferences']))
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <strong>Preferencias del Usuario:</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                @php
                                    $preferences = is_array($user['preferences']) ?
                                        $user['preferences'] :
                                        json_decode($user['preferences'], true);
                                @endphp
                                @if($preferences && count($preferences) > 0)
                                    <pre class="small mb-0">{{ json_encode($preferences, JSON_PRETTY_PRINT) }}</pre>
                                @else
                                    <span class="text-muted">Sin preferencias configuradas</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <strong>Permisos por Rol:</strong>
                        <div class="mt-2">
                            @if($user['role'] === 'super_admin')
                                <div class="alert alert-danger">
                                    <strong>Super Administrador</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Acceso total al sistema</li>
                                        <li>Gestión de usuarios y roles</li>
                                        <li>Configuraciones del sistema</li>
                                        <li>Backup y mantenimiento</li>
                                        <li>Logs y auditoría</li>
                                    </ul>
                                </div>
                            @elseif($user['role'] === 'admin')
                                <div class="alert alert-primary">
                                    <strong>Administrador</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Gestión completa del contenido</li>
                                        <li>Crear, editar y eliminar proyectos</li>
                                        <li>Gestionar educación y experiencia</li>
                                        <li>Configurar perfil y enlaces sociales</li>
                                        <li>Ver estadísticas del sistema</li>
                                    </ul>
                                </div>
                            @elseif($user['role'] === 'editor')
                                <div class="alert alert-info">
                                    <strong>Editor</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Editar contenido existente</li>
                                        <li>Actualizar proyectos</li>
                                        <li>Modificar educación y experiencia</li>
                                        <li>Actualizar información del perfil</li>
                                    </ul>
                                </div>
                            @else
                                <div class="alert alert-secondary">
                                    <strong>Visualizador</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Solo lectura del contenido</li>
                                        <li>Ver proyectos y estadísticas</li>
                                        <li>Acceso al dashboard</li>
                                        <li>Sin permisos de edición</li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Acciones -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Acciones
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user['id']) }}"
                       class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>
                        Editar Usuario
                    </a>

                    @if($user['is_active'] ?? true)
                        <form action="{{ route('admin.users.deactivate', $user['id']) }}"
                              method="POST"
                              onsubmit="return confirm('¿Desactivar este usuario? No podrá acceder al sistema.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-pause me-2"></i>
                                Desactivar Usuario
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.activate', $user['id']) }}"
                              method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-play me-2"></i>
                                Activar Usuario
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.users.index') }}"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Volver a la Lista
                    </a>

                    <hr>

                    @if($user['role'] !== 'super_admin' || count(array_filter($users ?? [], fn($u) => $u['role'] === 'super_admin')) > 1)
                        <form action="{{ route('admin.users.destroy', $user['id']) }}"
                              method="POST"
                              onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>
                                Eliminar Usuario
                            </button>
                        </form>
                    @else
                        <button class="btn btn-outline-danger w-100"
                                disabled
                                title="No se puede eliminar el último Super Admin">
                            <i class="bi bi-trash me-2"></i>
                            Eliminar Usuario
                        </button>
                        <small class="text-muted">No se puede eliminar el último Super Admin</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información del Sistema -->
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Información del Sistema
                </h6>
            </div>
            <div class="card-body">
                <div class="small text-muted">
                    <div class="mb-2">
                        <strong>ID:</strong> {{ $user['id'] ?? 'No disponible' }}
                    </div>
                    <div class="mb-2">
                        <strong>Registrado:</strong>
                        {{ !empty($user['created_at']) ? \Carbon\Carbon::parse($user['created_at'])->format('d/m/Y H:i') : 'No disponible' }}
                    </div>
                    @if(!empty($user['updated_at']) && $user['updated_at'] !== $user['created_at'])
                        <div class="mb-2">
                            <strong>Última actualización:</strong>
                            {{ \Carbon\Carbon::parse($user['updated_at'])->format('d/m/Y H:i') }}
                        </div>
                    @endif
                    @if(!empty($user['last_login']))
                        @php
                            $lastLogin = \Carbon\Carbon::parse($user['last_login']);
                            $now = \Carbon\Carbon::now();
                            $daysSinceLogin = $lastLogin->diffInDays($now);
                        @endphp
                        <div class="mb-2">
                            <strong>Actividad:</strong>
                            @if($daysSinceLogin < 7)
                                <span class="text-success">Reciente</span>
                            @elseif($daysSinceLogin < 30)
                                <span class="text-warning">Moderada</span>
                            @else
                                <span class="text-danger">Inactivo</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(($user['role'] ?? '') === 'super_admin')
            <div class="card border-danger mt-3">
                <div class="card-body">
                    <h6 class="card-title text-danger">
                        <i class="bi bi-shield-exclamation me-2"></i>
                        Super Administrador
                    </h6>
                    <p class="small text-muted">
                        Este usuario tiene acceso completo al sistema. Cualquier cambio debe ser realizado con extrema precaución.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@endsection
