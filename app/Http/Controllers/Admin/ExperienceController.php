<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExperienceController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseServiceOptimized $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        try {
            $experiences = $this->supabase->getWorkExperience();

            // Ordenar por order_position y luego por end_date
            usort($experiences, function($a, $b) {
                $orderA = $a['order_position'] ?? 0;
                $orderB = $b['order_position'] ?? 0;

                if ($orderA == $orderB) {
                    $dateA = $a['end_date'] ? strtotime($a['end_date']) : time();
                    $dateB = $b['end_date'] ? strtotime($b['end_date']) : time();
                    return $dateB - $dateA; // Más reciente primero
                }

                return $orderA - $orderB;
            });

            return view('admin.experience.index', compact('experiences'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar la experiencia laboral: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin.experience.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'employment_type' => 'required|in:full-time,part-time,contract,freelance,internship,volunteer',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'location' => 'nullable|string|max:255',
            'location_type' => 'required|in:on-site,remote,hybrid',
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|array',
            'achievements' => 'nullable|array',
            'technologies' => 'nullable|array',
            'company_url' => 'nullable|url',
            'company_logo_url' => 'nullable|url',
            'company_logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'is_featured' => 'boolean',
            'order_position' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $validator->validated();

            // Manejar subida de logo de empresa
            if ($request->hasFile('company_logo')) {
                $file = $request->file('company_logo');
                $filename = 'company_logo_' . time() . '.' . $file->getClientOriginalExtension();

                // Subir a Supabase
                $logoUrl = $this->supabase->uploadImage($file, $filename);
                if ($logoUrl) {
                    $data['company_logo_url'] = $logoUrl;
                }
            }

            // Si is_current es true, end_date debe ser null
            if ($data['is_current'] ?? false) {
                $data['end_date'] = null;
            }

            // Procesar arrays
            $data = $this->processArrayFields($data);

            $result = $this->supabase->createWorkExperience($data);

            if ($result) {
                return redirect()->route('admin.experience.index')
                    ->with('success', 'Experiencia laboral agregada exitosamente');
            } else {
                return back()->with('error', 'Error al agregar la experiencia laboral')->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        try {
            $experience = $this->supabase->getWorkExperienceById($id);

            if (!$experience) {
                return redirect()->route('admin.experience.index')
                    ->with('error', 'Experiencia laboral no encontrada');
            }

            return view('admin.experience.show', compact('experience'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar la experiencia laboral: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $experience = $this->supabase->getWorkExperienceById($id);

            if (!$experience) {
                return redirect()->route('admin.experience.index')
                    ->with('error', 'Experiencia laboral no encontrada');
            }

            return view('admin.experience.edit', compact('experience'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar la experiencia laboral: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'company' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'employment_type' => 'required|in:full-time,part-time,contract,freelance,internship,volunteer',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'location' => 'nullable|string|max:255',
            'location_type' => 'required|in:on-site,remote,hybrid',
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|array',
            'achievements' => 'nullable|array',
            'technologies' => 'nullable|array',
            'company_url' => 'nullable|url',
            'company_logo_url' => 'nullable|url',
            'company_logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'is_featured' => 'boolean',
            'order_position' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $validator->validated();

            // Manejar subida de logo de empresa
            if ($request->hasFile('company_logo')) {
                $file = $request->file('company_logo');
                $filename = 'company_logo_' . time() . '.' . $file->getClientOriginalExtension();

                // Subir a Supabase
                $logoUrl = $this->supabase->uploadImage($file, $filename);
                if ($logoUrl) {
                    $data['company_logo_url'] = $logoUrl;
                }
            }

            // Si is_current es true, end_date debe ser null
            if ($data['is_current'] ?? false) {
                $data['end_date'] = null;
            }

            // Procesar arrays
            $data = $this->processArrayFields($data);

            $result = $this->supabase->updateWorkExperience($id, $data);

            if ($result) {
                return redirect()->route('admin.experience.index')
                    ->with('success', 'Experiencia laboral actualizada exitosamente');
            } else {
                return back()->with('error', 'Error al actualizar la experiencia laboral')->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->supabase->deleteWorkExperience($id);

            if ($result) {
                return redirect()->route('admin.experience.index')
                    ->with('success', 'Experiencia laboral eliminada exitosamente');
            } else {
                return back()->with('error', 'Error al eliminar la experiencia laboral');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Procesar campos de array (responsibilities, achievements, technologies)
     */
    private function processArrayFields(array $data): array
    {
        $arrayFields = ['responsibilities', 'achievements', 'technologies'];

        foreach ($arrayFields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = array_filter($data[$field], function($item) {
                    return !empty(trim($item));
                });
            }
        }

        return $data;
    }
}
