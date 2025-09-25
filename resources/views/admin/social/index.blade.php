@extends('layouts.admin')

@section('title', 'Enlaces Sociales')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-share text-primary me-2"></i>Enlaces Sociales
            </h1>
            <p class="text-muted">Gestiona tus redes sociales y enlaces</p>
        </div>
        <a href="{{ route('admin.social.create') }}" class="btn btn-primary">
            <i class="bi bi-plus me-1"></i>Nuevo Enlace
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(count($socialLinks) > 0)
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Enlaces Configurados</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Plataforma</th>
                                <th>URL</th>
                                <th>Icono</th>
                                <th>Color</th>
                                <th>Orden</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(collect($socialLinks)->sortBy('order') as $link)
                                <tr>
                                    <td>
                                        <strong>{{ ucfirst($link['platform']) }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ $link['url'] }}" target="_blank" class="text-decoration-none">
                                            {{ Str::limit($link['url'], 50) }}
                                            <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <i class="{{ $link['icon'] ?? 'bi-link' }}" style="color: {{ $link['color'] ?? '#666' }}"></i>
                                        <small class="text-muted">{{ $link['icon'] ?? 'bi-link' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $link['color'] ?? '#666' }}">
                                            {{ $link['color'] ?? '#666' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $link['order'] ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if($link['is_active'])
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.social.edit', $link['id']) }}"
                                               class="btn btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.social.destroy', $link['id']) }}"
                                                  style="display: inline-block;"
                                                  onsubmit="return confirm('¿Seguro que deseas eliminar este enlace?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <h5><i class="bi bi-info-circle me-2"></i>No hay enlaces sociales</h5>
            <p class="mb-0">
                No has configurado ningún enlace social aún.
                <a href="{{ route('admin.social.create') }}">Agrega el primero</a>
            </p>
        </div>
    @endif
</div>
@endsection
