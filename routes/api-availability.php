<?php

use Illuminate\Support\Facades\Route;
use App\Services\SupabaseServiceOptimized;

// Ruta pública para obtener disponibilidad (para el portafolio)
Route::get('/api/availability', function () {
    $service = new SupabaseServiceOptimized();

    try {
        $availability = $service->getAvailabilityForPortfolio();

        return response()->json([
            'status' => $availability['status'] ?? 'available',
            'response_time' => $availability['response_time'] ?? '24 horas',
            'message' => $availability['custom_message'] ?? null,
            'show_calendar' => $availability['show_calendar_link'] ?? false,
            'calendar_url' => $availability['calendar_url'] ?? null,
            'details' => $availability['availability_details'] ?? null
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'available',
            'response_time' => '24 horas',
            'message' => 'Disponible para nuevos proyectos',
            'show_calendar' => false,
            'calendar_url' => null,
            'details' => null
        ]);
    }
});
