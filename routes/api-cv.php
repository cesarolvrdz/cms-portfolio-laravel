<?php

// API pública para CV
use App\Services\SupabaseServiceOptimized;

// Obtener CV actual
Route::get('/api/cv', function () {
    try {
        $supabaseService = app(SupabaseServiceOptimized::class);
        $cv = $supabaseService->getCurrentCv();

        if (!$cv) {
            return response()->json([
                'success' => false,
                'message' => 'No hay CV disponible'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $cv['id'],
                'title' => $cv['title'],
                'description' => $cv['description'],
                'version' => $cv['version'],
                'language' => $cv['language'],
                'file_url' => $cv['file_url'],
                'file_size' => $cv['file_size'],
                'updated_at' => $cv['updated_at']
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener CV',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Obtener CV por idioma
Route::get('/api/cv/{language}', function ($language) {
    try {
        $supabaseService = app(SupabaseServiceOptimized::class);
        $cvDocuments = $supabaseService->getCvDocuments(true);

        // Buscar CV por idioma
        $cv = collect($cvDocuments)->firstWhere('language', $language);

        if (!$cv) {
            return response()->json([
                'success' => false,
                'message' => "No hay CV disponible en idioma: $language"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $cv['id'],
                'title' => $cv['title'],
                'description' => $cv['description'],
                'version' => $cv['version'],
                'language' => $cv['language'],
                'file_url' => $cv['file_url'],
                'file_size' => $cv['file_size'],
                'is_current' => $cv['is_current'],
                'updated_at' => $cv['updated_at']
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener CV por idioma',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Descargar CV actual (incrementa contador)
Route::get('/api/cv/download', function () {
    try {
        $supabaseService = app(SupabaseServiceOptimized::class);
        $cv = $supabaseService->getCurrentCv();

        if (!$cv) {
            return response()->json([
                'success' => false,
                'message' => 'No hay CV disponible para descarga'
            ], 404);
        }

        // Aquí se podría incrementar el contador de descargas
        // pero por simplicidad lo omitimos en esta implementación

        return redirect($cv['file_url']);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al descargar CV',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Obtener lista de idiomas disponibles
Route::get('/api/cv/languages', function () {
    try {
        $supabaseService = app(SupabaseServiceOptimized::class);
        $cvDocuments = $supabaseService->getCvDocuments(true);

        $languages = collect($cvDocuments)
            ->pluck('language')
            ->unique()
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'data' => $languages,
            'count' => count($languages)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener idiomas disponibles',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Obtener información básica del CV (sin URL de descarga)
Route::get('/api/cv/info', function () {
    try {
        $supabaseService = app(SupabaseServiceOptimized::class);
        $cv = $supabaseService->getCurrentCv();

        if (!$cv) {
            return response()->json([
                'success' => false,
                'message' => 'No hay CV disponible'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'title' => $cv['title'],
                'description' => $cv['description'],
                'version' => $cv['version'],
                'language' => $cv['language'],
                'file_size' => $cv['file_size'],
                'last_updated' => $cv['updated_at']
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener información del CV',
            'error' => $e->getMessage()
        ], 500);
    }
});
