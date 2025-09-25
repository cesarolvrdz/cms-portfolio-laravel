@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')
@section('page-title', 'Gestión de Usuarios del CMS')
@section('page-description', 'Administra los usuarios con acceso al sistema de gestión')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="bi bi-people me-2"></i>
            Usuarios del Sistema
        </h4>
        <small class="text-muted">Total: {{ count($users ?? []) }} usuarios</small>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-2"></i>
        Nuevo Usuario
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if(!empty($users) && count($users) > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Último Acceso</th>
                            <th style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="{{ !($user['is_active'] ?? true) ? 'table-secondary' : '' }}">
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    @if(!empty($user['avatar_url']))
                                        <img src="{{ $user['avatar_url'] }}"
                                             alt="Avatar"
                                             class="rounded-circle me-3"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-person text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $user['name'] ?? 'Sin nombre' }}</strong>
                                        @if($user['two_factor_enabled'] ?? false)
                                            <br><small class="text-success">
                                                <i class="bi bi-shield-check me-1"></i>2FA Habilitado
                                            </small>
                                        @endif
                                        @if(!empty($user['email_verified_at']))
                                            <br><small class="text-success">
                                                <i class="bi bi-check-circle me-1"></i>Verificado
                                            </small>
                                        @else
                                            <br><small class="text-warning">
                                                <i class="bi bi-exclamation-circle me-1"></i>Sin verificar
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="text-muted">{{ $user['email'] ?? 'Sin email' }}</span>
                            </td>
                            <td class="align-middle">
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
                                <span class="badge bg-{{ $roleInfo['class'] }}">
                                    {{ $roleInfo['text'] }}
                                </span>
                            </td>
                            <td class="align-middle">
                                @if($user['is_active'] ?? true)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if(!empty($user['last_login']))
                                    @php
                                        $lastLogin = \Carbon\Carbon::parse($user['last_login']);
                                    @endphp
                                    <small class="text-muted">
                                        {{ $lastLogin->diffForHumans() }}
                                        <br>
                                        {{ $lastLogin->format('d/m/Y H:i') }}
                                    </small>
                                @else
                                    <small class="text-muted">Nunca</small>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.show', $user['id']) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user['id']) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    @if($user['is_active'] ?? true)
                                        <form action="{{ route('admin.users.deactivate', $user['id']) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('¿Desactivar este usuario?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-warning"
                                                    title="Desactivar">
                                                <i class="bi bi-pause"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.users.activate', $user['id']) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-success"
                                                    title="Activar">
                                                <i class="bi bi-play"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($user['role'] !== 'super_admin' || count(array_filter($users, fn($u) => $u['role'] === 'super_admin')) > 1)
                                        <form action="{{ route('admin.users.destroy', $user['id']) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-outline-danger"
                                                disabled
                                                title="No se puede eliminar el último Super Admin">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people text-muted mb-3" style="font-size: 3rem;"></i>
                <h5 class="text-muted">No hay usuarios registrados</h5>
                <p class="text-muted mb-4">Comienza creando el primer usuario del sistema.</p>

                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('admin.setup.index') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>
                        Crear Primer Usuario
                    </a>

                    <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus me-2"></i>
                        Nuevo Usuario
                    </a>
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

<!-- Información sobre roles -->
<div class="card mt-4 bg-light">
    <div class="card-body">
        <h6 class="card-title">
            <i class="bi bi-info-circle me-2"></i>
            Información sobre Roles
        </h6>
        <div class="row">
            <div class="col-md-3">
                <span class="badge bg-danger">Super Admin</span>
                <small class="d-block text-muted">Acceso total al sistema</small>
            </div>
            <div class="col-md-3">
                <span class="badge bg-primary">Administrador</span>
                <small class="d-block text-muted">Gestión completa del contenido</small>
            </div>
            <div class="col-md-3">
                <span class="badge bg-info">Editor</span>
                <small class="d-block text-muted">Edición de contenido</small>
            </div>
            <div class="col-md-3">
                <span class="badge bg-secondary">Visualizador</span>
                <small class="d-block text-muted">Solo lectura</small>
            </div>
        </div>
    </div>
</div>
@endsection
