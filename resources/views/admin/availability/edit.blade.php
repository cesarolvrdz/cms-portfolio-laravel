@extends('layouts.admin')

@section('title', 'Editar Disponibilidad')
@section('page-title', 'Editar Disponibilidad')
@section('page-description', 'Configura tu estado de disponibilidad para el portafolio')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    Configurar Disponibilidad
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.availability.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">
                                    <i class="bi bi-circle-fill me-1"></i>
                                    Estado de Disponibilidad <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="available" {{ old('status', $availability['status'] ?? '') == 'available' ? 'selected' : '' }}>
                                        🟢 Disponible - Aceptando nuevos proyectos
                                    </option>
                                    <option value="busy" {{ old('status', $availability['status'] ?? '') == 'busy' ? 'selected' : '' }}>
                                        🟡 Ocupado - Respuesta más lenta
                                    </option>
                                    <option value="unavailable" {{ old('status', $availability['status'] ?? '') == 'unavailable' ? 'selected' : '' }}>
                                        🔴 No Disponible - No aceptando proyectos
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="response_time" class="form-label">
                                    <i class="bi bi-stopwatch me-1"></i>
                                    Tiempo de Respuesta <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="response_time" id="response_time"
                                       class="form-control @error('response_time') is-invalid @enderror"
                                       value="{{ old('response_time', $availability['response_time'] ?? '24 horas') }}"
                                       placeholder="Ej: 24 horas, 2-3 días, 1 semana" required>
                                @error('response_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="custom_message" class="form-label">
                            <i class="bi bi-chat-left-text me-1"></i>
                            Mensaje Personalizado
                        </label>
                        <textarea name="custom_message" id="custom_message" rows="3"
                                  class="form-control @error('custom_message') is-invalid @enderror"
                                  placeholder="Mensaje que se mostrará en tu portafolio (opcional)">{{ old('custom_message', $availability['custom_message'] ?? '') }}</textarea>
                        @error('custom_message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Este mensaje aparecerá en la sección de disponibilidad de tu portafolio.</div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">
                        <i class="bi bi-calendar me-2"></i>
                        Configuración de Calendario
                    </h6>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="show_calendar_link" id="show_calendar_link" value="1"
                                   {{ old('show_calendar_link', $availability['show_calendar_link'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_calendar_link">
                                Mostrar enlace de calendario en el portafolio
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="calendar_url" class="form-label">
                            <i class="bi bi-link me-1"></i>
                            URL del Calendario (opcional)
                        </label>
                        <input type="url" name="calendar_url" id="calendar_url"
                               class="form-control @error('calendar_url') is-invalid @enderror"
                               value="{{ old('calendar_url', $availability['calendar_url'] ?? '') }}"
                               placeholder="https://calendly.com/tu-usuario">
                        @error('calendar_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Enlace a tu sistema de reservas (Calendly, Cal.com, etc.)</div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">
                        <i class="bi bi-gear me-2"></i>
                        Detalles Adicionales
                    </h6>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="preferred_contact" class="form-label">
                                    <i class="bi bi-envelope me-1"></i>
                                    Método de Contacto Preferido
                                </label>
                                <select name="preferred_contact" id="preferred_contact" class="form-select @error('preferred_contact') is-invalid @enderror">
                                    <option value="">Seleccionar...</option>
                                    <option value="email" {{ old('preferred_contact', $availability['availability_details']['preferred_contact'] ?? '') == 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="whatsapp" {{ old('preferred_contact', $availability['availability_details']['preferred_contact'] ?? '') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                    <option value="telegram" {{ old('preferred_contact', $availability['availability_details']['preferred_contact'] ?? '') == 'telegram' ? 'selected' : '' }}>Telegram</option>
                                    <option value="form" {{ old('preferred_contact', $availability['availability_details']['preferred_contact'] ?? '') == 'form' ? 'selected' : '' }}>Formulario de contacto</option>
                                </select>
                                @error('preferred_contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="timezone" class="form-label">
                                    <i class="bi bi-globe me-1"></i>
                                    Zona Horaria
                                </label>
                                <input type="text" name="timezone" id="timezone"
                                       class="form-control @error('timezone') is-invalid @enderror"
                                       value="{{ old('timezone', $availability['availability_details']['timezone'] ?? 'America/Mexico_City') }}"
                                       placeholder="America/Mexico_City">
                                @error('timezone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="working_hours" class="form-label">
                                    <i class="bi bi-clock me-1"></i>
                                    Horario de Trabajo
                                </label>
                                <input type="text" name="working_hours" id="working_hours"
                                       class="form-control @error('working_hours') is-invalid @enderror"
                                       value="{{ old('working_hours', $availability['availability_details']['working_hours'] ?? '9:00 AM - 6:00 PM') }}"
                                       placeholder="9:00 AM - 6:00 PM">
                                @error('working_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.availability.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-eye me-2"></i>
                    Vista Previa
                </h6>
            </div>
            <div class="card-body">
                <div id="preview-section">
                    <div class="text-center">
                        <div id="preview-status" class="badge bg-success mb-2">Disponible</div>
                        <p class="mb-2"><strong>Tiempo de respuesta:</strong> <span id="preview-time">24 horas</span></p>
                        <p id="preview-message" class="text-muted small">Mensaje personalizado aparecerá aquí...</p>
                    </div>
                </div>
                <hr>
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Esta es una vista previa aproximada de cómo se verá en tu portafolio.
                </small>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    Consejos
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2">💡 Mantén actualizado tu estado según tu carga de trabajo actual</li>
                    <li class="mb-2">⏰ Sé realista con los tiempos de respuesta</li>
                    <li class="mb-2">📝 Un mensaje personalizado ayuda a dar contexto</li>
                    <li class="mb-0">📅 El enlace de calendario facilita las citas</li>
                </ul>
            </div>
        </div>
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

<script>
// Vista previa en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const timeInput = document.getElementById('response_time');
    const messageInput = document.getElementById('custom_message');

    const previewStatus = document.getElementById('preview-status');
    const previewTime = document.getElementById('preview-time');
    const previewMessage = document.getElementById('preview-message');

    function updatePreview() {
        // Actualizar estado
        const status = statusSelect.value;
        let statusText = '';
        let badgeClass = '';

        switch(status) {
            case 'available':
                statusText = 'Disponible';
                badgeClass = 'bg-success';
                break;
            case 'busy':
                statusText = 'Ocupado';
                badgeClass = 'bg-warning';
                break;
            case 'unavailable':
                statusText = 'No Disponible';
                badgeClass = 'bg-danger';
                break;
        }

        previewStatus.textContent = statusText;
        previewStatus.className = 'badge ' + badgeClass + ' mb-2';

        // Actualizar tiempo
        previewTime.textContent = timeInput.value || '24 horas';

        // Actualizar mensaje
        const message = messageInput.value || 'Mensaje personalizado aparecerá aquí...';
        previewMessage.textContent = message;
    }

    statusSelect.addEventListener('change', updatePreview);
    timeInput.addEventListener('input', updatePreview);
    messageInput.addEventListener('input', updatePreview);

    // Inicializar
    updatePreview();
});
</script>
@endsection
