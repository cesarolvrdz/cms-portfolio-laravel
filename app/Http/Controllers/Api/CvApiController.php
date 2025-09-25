<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CvApiController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseServiceOptimized $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Get all CV documents
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $filters = [];

            // Filtro por idioma
            if ($request->has('language')) {
                $filters['language'] = 'eq.' . $request->language;
            }

            // Solo CVs actuales
            if ($request->boolean('current_only')) {
                $filters['is_current'] = 'eq.true';
            }

            $cvs = $this->supabaseService->getCvDocumentsWithFilters($filters);

            return response()->json([
                'success' => true,
                'data' => $cvs,
                'message' => 'CVs obtenidos exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en CV API index: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los CVs',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * Get current CV by language
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function current(Request $request)
    {
        try {
            $language = $request->get('language', 'es');

            $cv = $this->supabaseService->getCurrentCvByLanguage($language);

            if (!$cv) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay CV actual para el idioma especificado',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cv,
                'message' => 'CV actual obtenido exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en CV API current: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el CV actual',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * Get specific CV document by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $cvs = $this->supabaseService->getCvDocuments();
            $cv = collect($cvs)->firstWhere('id', (int)$id);

            if (!$cv) {
                return response()->json([
                    'success' => false,
                    'message' => 'CV no encontrado',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cv,
                'message' => 'CV obtenido exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en CV API show: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el CV',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * Download CV and increment download counter
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        try {
            $cvs = $this->supabaseService->getCvDocuments();
            $cv = collect($cvs)->firstWhere('id', (int)$id);

            if (!$cv) {
                return response()->json([
                    'success' => false,
                    'message' => 'CV no encontrado'
                ], 404);
            }

            // Incrementar contador de descargas
            $this->supabaseService->incrementCvDownloadCount($id);

            // Redirigir al archivo
            return redirect($cv['file_url']);

        } catch (\Exception $e) {
            Log::error('Error en CV API download: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al descargar el CV',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * Get download statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        try {
            $cvs = $this->supabaseService->getCvDocuments();

            $stats = [
                'total_documents' => count($cvs),
                'total_downloads' => array_sum(array_column($cvs, 'download_count')),
                'languages' => array_unique(array_column($cvs, 'language')),
                'current_cvs' => array_filter($cvs, function($cv) {
                    return $cv['is_current'];
                }),
                'by_language' => []
            ];

            // Estadísticas por idioma
            foreach ($cvs as $cv) {
                $lang = $cv['language'];
                if (!isset($stats['by_language'][$lang])) {
                    $stats['by_language'][$lang] = [
                        'total' => 0,
                        'downloads' => 0,
                        'current' => null
                    ];
                }

                $stats['by_language'][$lang]['total']++;
                $stats['by_language'][$lang]['downloads'] += $cv['download_count'];

                if ($cv['is_current']) {
                    $stats['by_language'][$lang]['current'] = $cv;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Estadísticas obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en CV API stats: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }
}
