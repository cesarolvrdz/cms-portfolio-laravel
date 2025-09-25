<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EducationController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseServiceOptimized $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        try {
            $education = $this->supabase->getEducation();

            // Ordenar por order_position y luego por end_date
            usort($education, function($a, $b) {
                $orderA = $a['order_position'] ?? 0;
                $orderB = $b['order_position'] ?? 0;

                if ($orderA == $orderB) {
                    $dateA = $a['end_date'] ? strtotime($a['end_date']) : time();
                    $dateB = $b['end_date'] ? strtotime($b['end_date']) : time();
                    return $dateB - $dateA; // Más reciente primero
                }

                return $orderA - $orderB;
            });

            return view('admin.education.index', compact('education'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar la formación académica: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin.education.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institution' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'grade' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'institution_url' => 'nullable|url',
            'certificate_url' => 'nullable|url',
            'type' => 'required|in:degree,certification,course,bootcamp,workshop',
            'is_featured' => 'boolean',
            'order_position' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $validator->validated();

            // Si is_current es true, end_date debe ser null
            if ($data['is_current'] ?? false) {
                $data['end_date'] = null;
            }

            $result = $this->supabase->createEducation($data);

            if ($result) {
                return redirect()->route('admin.education.index')
                    ->with('success', 'Formación académica agregada exitosamente');
            } else {
                return back()->with('error', 'Error al agregar la formación académica')->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        try {
            $education = $this->supabase->getEducationById($id);

            if (!$education) {
                return redirect()->route('admin.education.index')
                    ->with('error', 'Formación académica no encontrada');
            }

            return view('admin.education.show', compact('education'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar la formación académica: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $education = $this->supabase->getEducationById($id);

            if (!$education) {
                return redirect()->route('admin.education.index')
                    ->with('error', 'Formación académica no encontrada');
            }

            return view('admin.education.edit', compact('education'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar la formación académica: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'institution' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'grade' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'institution_url' => 'nullable|url',
            'certificate_url' => 'nullable|url',
            'type' => 'required|in:degree,certification,course,bootcamp,workshop',
            'is_featured' => 'boolean',
            'order_position' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $validator->validated();

            // Si is_current es true, end_date debe ser null
            if ($data['is_current'] ?? false) {
                $data['end_date'] = null;
            }

            $result = $this->supabase->updateEducation($id, $data);

            if ($result) {
                return redirect()->route('admin.education.index')
                    ->with('success', 'Formación académica actualizada exitosamente');
            } else {
                return back()->with('error', 'Error al actualizar la formación académica')->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->supabase->deleteEducation($id);

            if ($result) {
                return redirect()->route('admin.education.index')
                    ->with('success', 'Formación académica eliminada exitosamente');
            } else {
                return back()->with('error', 'Error al eliminar la formación académica');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
