<?php

// API pública para certificados
use App\Services\SupabaseServiceOptimized;

// Obtener todos los certificados públicos
Route::get('/api/certificates', function () {
    try {
        $supabaseService = app(SupabaseServiceOptimized::class);
        $certificates = $supabaseService->getCertificatesForPortfolio();

        return response()->json([
            'success' => true,
            'data' => $certificates,
            'count' => count($certificates)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener certificados',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Obtener certificados por categoría
Route::get('/api/certificates/category/{category}', function ($category) {
    try {
        $supabaseService = app(SupabaseServiceOptimized::class);
        $certificates = $supabaseService->getCertificates(true, $category);

        return response()->json([
            'success' => true,
            'data' => $certificates,
            'category' => $category,
            'count' => count($certificates)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener certificados por categoría',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Obtener solo certificados destacados
Route::get('/api/certificates/featured', function () {
    try {
        $supabaseService = app(SupabaseServiceOptimized::class);
        $certificates = $supabaseService->getCertificates(true, null, true);

        return response()->json([
            'success' => true,
            'data' => $certificates,
            'count' => count($certificates)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener certificados destacados',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Obtener categorías de certificados
Route::get('/api/certificates/categories', function () {
    try {
        $supabaseService = app(SupabaseServiceOptimized::class);
        $categories = $supabaseService->getCertificateCategories();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'count' => count($categories)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener categorías',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Obtener certificado específico por ID
Route::get('/api/certificates/{id}', function ($id) {
    try {
        $supabaseService = app(SupabaseServiceOptimized::class);
        $certificate = $supabaseService->getCertificateById($id);

        if (!$certificate || !$certificate['is_active']) {
            return response()->json([
                'success' => false,
                'message' => 'Certificado no encontrado o no disponible'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $certificate
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener certificado',
            'error' => $e->getMessage()
        ], 500);
    }
});
