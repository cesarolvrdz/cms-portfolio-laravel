@extends('layouts.admin')

@section('title', 'Configuraciones del Sitio')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-gear text-primary me-2"></i>Configuraciones del Sitio
            </h1>
            <p class="text-muted">Gestiona la configuración global del portafolio</p>
        </div>
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

    @if($groupedSettings->isNotEmpty())
        <div class="row">
            @foreach($groupedSettings as $groupName => $settings)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                @php
                                    $icons = [
                                        'general' => 'bi-globe',
                                        'seo' => 'bi-search',
                                        'appearance' => 'bi-palette',
                                        'contact' => 'bi-envelope',
                                        'content' => 'bi-pencil',
                                        'sections' => 'bi-grid',
                                        'pagination' => 'bi-list',
                                        'tracking' => 'bi-graph-up',
                                        'meta' => 'bi-info'
                                    ];
                                    $icon = $icons[$groupName] ?? 'bi-gear';
                                @endphp
                                <i class="bi {{ $icon }} me-2"></i>
                                {{ ucfirst($groupName) }}
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach($settings as $setting)
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">{{ $setting['label'] }}</label>
                                    @if($setting['description'])
                                        <div class="text-muted small mb-1">{{ $setting['description'] }}</div>
                                    @endif

                                    <form method="POST" action="{{ route('admin.settings.update', $setting['key']) }}" class="d-flex">
                                        @csrf
                                        @method('PUT')

                                        @if($setting['type'] === 'boolean')
                                            <select name="value" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="true" {{ $setting['value'] === 'true' ? 'selected' : '' }}>Sí</option>
                                                <option value="false" {{ $setting['value'] === 'false' ? 'selected' : '' }}>No</option>
                                            </select>
                                        @elseif($setting['type'] === 'textarea')
                                            <textarea name="value" class="form-control form-control-sm" rows="2">{{ $setting['value'] }}</textarea>
                                            <button type="submit" class="btn btn-outline-primary btn-sm ms-1">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        @elseif($setting['type'] === 'color' || $setting['key'] === 'theme_color' || $setting['key'] === 'accent_color')
                                            <input type="color" name="value" class="form-control form-control-color form-control-sm"
                                                   value="{{ $setting['value'] }}" onchange="this.form.submit()">
                                        @else
                                            <input type="{{ $setting['type'] === 'text' ? 'text' : $setting['type'] }}"
                                                   name="value" class="form-control form-control-sm"
                                                   value="{{ $setting['value'] }}">
                                            <button type="submit" class="btn btn-outline-primary btn-sm ms-1">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        @endif
                                    </form>

                                    @if($setting['is_public'])
                                        <span class="badge bg-success badge-sm mt-1">Público</span>
                                    @else
                                        <span class="badge bg-secondary badge-sm mt-1">Privado</span>
                                    @endif
                                </div>
                                @if(!$loop->last)
                                    <hr>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            <h5><i class="bi bi-info-circle me-2"></i>No hay configuraciones</h5>
            <p class="mb-0">No se encontraron configuraciones del sitio.</p>
        </div>
    @endif
</div>
@endsection
