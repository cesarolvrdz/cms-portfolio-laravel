<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\CvApiController;

Route::prefix('v1')->group(function () {
    // Rutas de proyectos
    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('projects/{id}', [ProjectController::class, 'show']);
    Route::post('projects', [ProjectController::class, 'store'])->middleware('auth:supabase');
    Route::put('projects/{id}', [ProjectController::class, 'update'])->middleware('auth:supabase');
    Route::delete('projects/{id}', [ProjectController::class, 'destroy'])->middleware('auth:supabase');

    // Rutas de CV (públicas para el portafolio)
    Route::prefix('cv')->group(function () {
        Route::get('/', [CvApiController::class, 'index']); // Obtener todos los CVs
        Route::get('/current', [CvApiController::class, 'current']); // Obtener CV actual por idioma
        Route::get('/stats', [CvApiController::class, 'stats']); // Estadísticas de descarga
        Route::get('/{id}', [CvApiController::class, 'show']); // Obtener CV específico
        Route::get('/{id}/download', [CvApiController::class, 'download']); // Descargar CV
    });
});
