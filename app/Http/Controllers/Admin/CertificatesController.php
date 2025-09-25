<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CertificatesController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseServiceOptimized $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Display a listing of certificates
     */
    public function index(Request $request)
    {
        try {
            // Obtener parámetros de filtro
            $category = $request->get('category');
            $featured = $request->get('featured');

            // Log para debugging (remover en producción)
            Log::info('Certificados filtros aplicados', [
                'category' => $category,
                'featured' => $featured
            ]);

            // Determinar si mostrar solo destacados
            $featuredOnly = $featured === '1';

            // Obtener certificados con filtros
            $certificates = $this->supabaseService->getCertificates(true, $category, $featuredOnly);

            // Si featured es '0' (no destacados), filtrar manualmente
            if ($featured === '0') {
                $certificates = array_filter($certificates, function($cert) {
                    return !($cert['is_featured'] ?? false);
                });
                // Reindexar el array después del filtro
                $certificates = array_values($certificates);
            }

            Log::info('Certificados obtenidos', [
                'total' => count($certificates),
                'filtered_by_category' => !empty($category),
                'filtered_by_featured' => !empty($featured)
            ]);

            $categories = $this->supabaseService->getCertificateCategories();

            return view('admin.certificates.index', compact('certificates', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error cargando certificados: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar los certificados.');
        }
    }

    /**
     * Show the form for creating a new certificate
     */
    public function create()
    {
        $categories = $this->supabaseService->getCertificateCategories();

        return view('admin.certificates.create', compact('categories'));
    }

    /**
     * Store a newly created certificate
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'credential_id' => 'nullable|string|max:100',
            'credential_url' => 'nullable|url|max:500',
            'skills' => 'nullable|string',
            'order_position' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10240', // Max 10MB
        ]);

        try {
            // Procesar imagen si existe
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = 'certificate_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imageUrl = $this->supabaseService->uploadImage($image, 'certificates/' . $imageName);
            }

            // Procesar PDF si existe
            $pdfUrl = null;
            if ($request->hasFile('pdf')) {
                $pdf = $request->file('pdf');
                $pdfName = 'certificate_' . time() . '_' . uniqid() . '.pdf';
                $pdfUrl = $this->supabaseService->uploadPdf($pdf, $pdfName, 'certificates');
            }

            // Procesar skills como array
            $skills = [];
            if ($validated['skills']) {
                $skills = array_map('trim', explode(',', $validated['skills']));
                $skills = array_filter($skills); // Remover elementos vacíos
            }

            $data = [
                'title' => $validated['title'],
                'institution' => $validated['institution'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'issue_date' => $validated['issue_date'],
                'expiry_date' => $validated['expiry_date'],
                'credential_id' => $validated['credential_id'],
                'credential_url' => $validated['credential_url'],
                'skills' => $skills,
                'order_position' => $validated['order_position'] ?? 0,
                'is_featured' => $validated['is_featured'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
                'image_url' => $imageUrl,
                'pdf_url' => $pdfUrl,
            ];

            $result = $this->supabaseService->createCertificate($data);

            if ($result) {
                return redirect()->route('admin.certificates.index')
                    ->with('success', 'Certificado creado exitosamente.');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error al crear el certificado.');
            }

        } catch (\Exception $e) {
            Log::error('Error creando certificado: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el certificado: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a certificate
     */
    public function edit($id)
    {
        try {
            $certificate = $this->supabaseService->getCertificateById($id);

            if (!$certificate) {
                return redirect()->route('admin.certificates.index')
                    ->with('error', 'Certificado no encontrado.');
            }

            $categories = $this->supabaseService->getCertificateCategories();

            return view('admin.certificates.edit', compact('certificate', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error cargando certificado: ' . $e->getMessage());
            return redirect()->route('admin.certificates.index')
                ->with('error', 'Error al cargar el certificado.');
        }
    }

    /**
     * Update a certificate
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'credential_id' => 'nullable|string|max:100',
            'credential_url' => 'nullable|url|max:500',
            'skills' => 'nullable|string',
            'order_position' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10240',
            'remove_image' => 'boolean',
            'remove_pdf' => 'boolean',
        ]);

        try {
            $certificate = $this->supabaseService->getCertificateById($id);

            if (!$certificate) {
                return redirect()->route('admin.certificates.index')
                    ->with('error', 'Certificado no encontrado.');
            }

            // Manejar imagen
            $imageUrl = $certificate['image_url'];
            if ($request->has('remove_image') && $request->remove_image) {
                $imageUrl = null;
            } elseif ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = 'certificate_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imageUrl = $this->supabaseService->uploadImage($image, 'certificates/' . $imageName);
            }

            // Manejar PDF
            $pdfUrl = $certificate['pdf_url'];
            if ($request->has('remove_pdf') && $request->remove_pdf) {
                $pdfUrl = null;
            } elseif ($request->hasFile('pdf')) {
                $pdf = $request->file('pdf');
                $pdfName = 'certificate_' . time() . '_' . uniqid() . '.pdf';
                $pdfUrl = $this->supabaseService->uploadPdf($pdf, $pdfName, 'certificates');
            }

            // Procesar skills como array
            $skills = [];
            if ($validated['skills']) {
                $skills = array_map('trim', explode(',', $validated['skills']));
                $skills = array_filter($skills);
            }

            $data = [
                'title' => $validated['title'],
                'institution' => $validated['institution'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'issue_date' => $validated['issue_date'],
                'expiry_date' => $validated['expiry_date'],
                'credential_id' => $validated['credential_id'],
                'credential_url' => $validated['credential_url'],
                'skills' => $skills,
                'order_position' => $validated['order_position'] ?? 0,
                'is_featured' => $validated['is_featured'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
                'image_url' => $imageUrl,
                'pdf_url' => $pdfUrl,
            ];

            $result = $this->supabaseService->updateCertificate($id, $data);

            if ($result) {
                return redirect()->route('admin.certificates.index')
                    ->with('success', 'Certificado actualizado exitosamente.');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error al actualizar el certificado.');
            }

        } catch (\Exception $e) {
            Log::error('Error actualizando certificado: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el certificado: ' . $e->getMessage());
        }
    }

    /**
     * Remove a certificate
     */
    public function destroy($id)
    {
        try {
            $certificate = $this->supabaseService->getCertificateById($id);

            if (!$certificate) {
                return redirect()->route('admin.certificates.index')
                    ->with('error', 'Certificado no encontrado.');
            }

            $result = $this->supabaseService->deleteCertificate($id);

            if ($result) {
                return redirect()->route('admin.certificates.index')
                    ->with('success', 'Certificado eliminado exitosamente.');
            } else {
                return redirect()->back()
                    ->with('error', 'Error al eliminar el certificado.');
            }

        } catch (\Exception $e) {
            Log::error('Error eliminando certificado: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al eliminar el certificado: ' . $e->getMessage());
        }
    }

    /**
     * Toggle featured status via AJAX
     */
    public function toggleFeatured(Request $request, $id)
    {
        try {
            $certificate = $this->supabaseService->getCertificateById($id);

            if (!$certificate) {
                return response()->json(['success' => false, 'message' => 'Certificado no encontrado.']);
            }

            $result = $this->supabaseService->updateCertificate($id, [
                'is_featured' => !$certificate['is_featured']
            ]);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'is_featured' => !$certificate['is_featured'],
                    'message' => 'Estado actualizado exitosamente.'
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Error al actualizar el estado.']);
            }

        } catch (\Exception $e) {
            Log::error('Error toggling featured status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno del servidor.']);
        }
    }

    /**
     * Toggle active status via AJAX
     */
    public function toggleActive(Request $request, $id)
    {
        try {
            $certificate = $this->supabaseService->getCertificateById($id);

            if (!$certificate) {
                return response()->json(['success' => false, 'message' => 'Certificado no encontrado.']);
            }

            $result = $this->supabaseService->updateCertificate($id, [
                'is_active' => !$certificate['is_active']
            ]);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'is_active' => !$certificate['is_active'],
                    'message' => 'Estado actualizado exitosamente.'
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Error al actualizar el estado.']);
            }

        } catch (\Exception $e) {
            Log::error('Error toggling active status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno del servidor.']);
        }
    }
}
