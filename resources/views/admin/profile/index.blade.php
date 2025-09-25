@extends('layouts.admin')

@section('title', 'Mi Perfil')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user text-primary me-2"></i>Mi Perfil
            </h1>
            <p class="text-muted">Información personal del portafolio</p>
        </div>
        <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i>Editar Perfil
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($profile)
        <div class="row">
            <!-- Avatar -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Foto de Perfil</h6>
                    </div>
                    <div class="card-body text-center">
                        @if(isset($profile['avatar_url']) && $profile['avatar_url'])
                            <img src="{{ $profile['avatar_url'] }}" alt="Foto de perfil"
                                 class="rounded-circle mb-3 shadow"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto shadow"
                                 style="width: 150px; height: 150px;">
                                <i class="bi bi-person-circle text-muted" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                        <h5 class="card-title mb-1">{{ $profile['name'] }}</h5>
                        <p class="text-muted">{{ $profile['title'] }}</p>
                    </div>
                </div>

                <!-- Habilidades -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Habilidades</h6>
                    </div>
                    <div class="card-body">
                        @if($profile['skills'])
                            @php
                                $skills = is_string($profile['skills']) ? json_decode($profile['skills'], true) : $profile['skills'];
                            @endphp
                            @if(is_array($skills) && count($skills) > 0)
                                @foreach($skills as $skill)
                                    <span class="badge bg-primary me-1 mb-2">{{ $skill }}</span>
                                @endforeach
                            @else
                                <p class="text-muted">No hay habilidades configuradas</p>
                            @endif
                        @else
                            <p class="text-muted">No hay habilidades configuradas</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Información Principal -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Información Personal</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Nombre:</strong></div>
                            <div class="col-sm-9">{{ $profile['name'] }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Título:</strong></div>
                            <div class="col-sm-9">{{ $profile['title'] }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Email:</strong></div>
                            <div class="col-sm-9">
                                <a href="mailto:{{ $profile['email'] }}">{{ $profile['email'] }}</a>
                            </div>
                        </div>
                        @if($profile['location'])
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Ubicación:</strong></div>
                                <div class="col-sm-9">{{ $profile['location'] }}</div>
                            </div>
                        @endif
                        @if($profile['phone'])
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Teléfono:</strong></div>
                                <div class="col-sm-9">{{ $profile['phone'] }}</div>
                            </div>
                        @endif
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Bio:</strong></div>
                            <div class="col-sm-9">{{ $profile['bio'] }}</div>
                        </div>
                        @if($profile['resume_url'])
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>CV:</strong></div>
                                <div class="col-sm-9">
                                    <a href="{{ $profile['resume_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Ver CV
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle me-2"></i>No hay perfil configurado</h5>
            <p class="mb-0">Parece que aún no has configurado tu perfil.
                <a href="{{ route('admin.profile.edit') }}">Créalo ahora</a>
            </p>
        </div>
    @endif
</div>
@endsection
