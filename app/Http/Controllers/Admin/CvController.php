<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CvController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseServiceOptimized $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Display a listing of CV documents
     */
    public function index()
    {
        try {
            $cvDocuments = $this->supabaseService->getCvDocuments(true);
            $currentCv = $this->supabaseService->getCurrentCv();

            return view('admin.cv.index', compact('cvDocuments', 'currentCv'));
        } catch (\Exception $e) {
            Log::error('Error cargando documentos CV: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar los documentos CV.');
        }
    }

    /**
     * Show the form for creating a new CV document
     */
    public function create()
    {
        return view('admin.cv.create');
    }

    /**
     * Store a newly created CV document
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'language' => 'required|string|max:10',
            'is_current' => 'boolean',
            'pdf' => 'required|mimes:pdf|max:20480', // Max 20MB
        ]);

        try {
            // Validar archivo PDF
            if (!$request->hasFile('pdf')) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Debe seleccionar un archivo PDF.');
            }

            $pdf = $request->file('pdf');

            // Validaciones adicionales
            if (!$pdf->isValid()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'El archivo PDF no es válido.');
            }

            $pdfSize = $pdf->getSize(); // Obtener tamaño antes de procesar
            $pdfName = 'cv_' . time() . '_' . uniqid() . '.pdf';

            Log::info('Iniciando subida de CV', [
                'filename' => $pdfName,
                'size' => $pdfSize,
                'mime' => $pdf->getMimeType()
            ]);

            $pdfUrl = $this->supabaseService->uploadPdf($pdf, $pdfName, 'cv');

            if (!$pdfUrl) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error al subir el archivo PDF. Verifique que el archivo sea válido.');
            }

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'version' => $validated['version'] ?? '1.0',
                'language' => $validated['language'],
                'file_url' => $pdfUrl,
                'file_size' => $pdfSize,
                'file_type' => 'pdf',
                'is_current' => $validated['is_current'] ?? false,
                'download_count' => 0,
            ];

            Log::info('Creando registro CV en base de datos', $data);

            $result = $this->supabaseService->createCvDocument($data);

            if ($result) {
                Log::info('CV creado exitosamente', ['id' => $result[0]['id'] ?? 'unknown']);
                return redirect()->route('admin.cv.index')
                    ->with('success', 'Documento CV creado exitosamente.');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error al guardar el documento CV en la base de datos.');
            }

        } catch (\Exception $e) {
            Log::error('Error creando documento CV: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el documento CV: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a CV document
     */
    public function edit($id)
    {
        try {
            $cvDocuments = $this->supabaseService->getCvDocuments(true);
            $cvDocument = collect($cvDocuments)->firstWhere('id', $id);

            if (!$cvDocument) {
                return redirect()->route('admin.cv.index')
                    ->with('error', 'Documento CV no encontrado.');
            }

            return view('admin.cv.edit', compact('cvDocument'));
        } catch (\Exception $e) {
            Log::error('Error cargando documento CV: ' . $e->getMessage());
            return redirect()->route('admin.cv.index')
                ->with('error', 'Error al cargar el documento CV.');
        }
    }

    /**
     * Update a CV document
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'language' => 'required|string|max:10',
            'is_current' => 'boolean',
            'pdf' => 'nullable|mimes:pdf|max:20480',
        ]);

        try {
            $cvDocuments = $this->supabaseService->getCvDocuments(true);
            $cvDocument = collect($cvDocuments)->firstWhere('id', $id);

            if (!$cvDocument) {
                return redirect()->route('admin.cv.index')
                    ->with('error', 'Documento CV no encontrado.');
            }

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'version' => $validated['version'] ?? $cvDocument['version'],
                'language' => $validated['language'],
                'is_current' => $validated['is_current'] ?? false,
            ];

            // Procesar nuevo archivo PDF si existe
            if ($request->hasFile('pdf')) {
                $pdf = $request->file('pdf');
                $pdfName = 'cv_' . time() . '_' . uniqid() . '.pdf';
                $pdfUrl = $this->supabaseService->uploadPdf($pdf, $pdfName, 'cv');

                if ($pdfUrl) {
                    $data['file_url'] = $pdfUrl;
                    $data['file_size'] = $pdf->getSize();
                }
            }

            $result = $this->supabaseService->updateCvDocument($id, $data);

            if ($result) {
                return redirect()->route('admin.cv.index')
                    ->with('success', 'Documento CV actualizado exitosamente.');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error al actualizar el documento CV.');
            }

        } catch (\Exception $e) {
            Log::error('Error actualizando documento CV: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el documento CV: ' . $e->getMessage());
        }
    }

    /**
     * Remove a CV document
     */
    public function destroy($id)
    {
        try {
            $cvDocuments = $this->supabaseService->getCvDocuments(true);
            $cvDocument = collect($cvDocuments)->firstWhere('id', $id);

            if (!$cvDocument) {
                return redirect()->route('admin.cv.index')
                    ->with('error', 'Documento CV no encontrado.');
            }

            $result = $this->supabaseService->deleteCvDocument($id);

            if ($result) {
                return redirect()->route('admin.cv.index')
                    ->with('success', 'Documento CV eliminado exitosamente.');
            } else {
                return redirect()->back()
                    ->with('error', 'Error al eliminar el documento CV.');
            }

        } catch (\Exception $e) {
            Log::error('Error eliminando documento CV: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al eliminar el documento CV: ' . $e->getMessage());
        }
    }

    /**
     * Set as current CV via AJAX
     */
    public function setCurrent(Request $request, $id)
    {
        try {
            $cvDocuments = $this->supabaseService->getCvDocuments(true);
            $cvDocument = collect($cvDocuments)->firstWhere('id', $id);

            if (!$cvDocument) {
                return response()->json(['success' => false, 'message' => 'Documento CV no encontrado.']);
            }

            $result = $this->supabaseService->setCurrentCv($id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'CV establecido como actual exitosamente.'
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Error al establecer como CV actual.']);
            }

        } catch (\Exception $e) {
            Log::error('Error setting current CV: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno del servidor.']);
        }
    }

    /**
     * Download CV document
     */
    public function download($id)
    {
        try {
            $cvDocuments = $this->supabaseService->getCvDocuments(true);
            $cvDocument = collect($cvDocuments)->firstWhere('id', $id);

            if (!$cvDocument) {
                abort(404, 'Documento CV no encontrado');
            }

            // Incrementar contador de descargas (esto se puede hacer en el frontend también)
            // Por simplicidad, lo omitimos aquí pero se puede implementar

            return redirect($cvDocument['file_url']);

        } catch (\Exception $e) {
            Log::error('Error downloading CV: ' . $e->getMessage());
            abort(500, 'Error al descargar el CV');
        }
    }

    /**
     * Preview CV document
     */
    public function preview($id)
    {
        try {
            $cvDocuments = $this->supabaseService->getCvDocuments(true);
            $cvDocument = collect($cvDocuments)->firstWhere('id', $id);

            if (!$cvDocument) {
                abort(404, 'Documento CV no encontrado');
            }

            return view('admin.cv.preview', compact('cvDocument'));

        } catch (\Exception $e) {
            Log::error('Error previewing CV: ' . $e->getMessage());
            abort(500, 'Error al previsualizar el CV');
        }
    }
}
