<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AvailabilityController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseServiceOptimized $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        try {
            $availability = $this->supabase->getAvailability();

            return view('admin.availability.index', compact('availability'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar la disponibilidad: ' . $e->getMessage());
        }
    }

    public function edit()
    {
        try {
            $availability = $this->supabase->getAvailability();

            if (!$availability) {
                // Si no existe, crear uno por defecto
                $defaultData = [
                    'status' => 'available',
                    'response_time' => '24 horas',
                    'custom_message' => 'Actualmente disponible para nuevos proyectos',
                    'show_calendar_link' => true,
                    'is_active' => true
                ];

                $availability = $this->supabase->createAvailability($defaultData);
            }

            return view('admin.availability.edit', compact('availability'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar la disponibilidad: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:available,busy,unavailable',
            'response_time' => 'required|string|max:50',
            'custom_message' => 'nullable|string|max:500',
            'show_calendar_link' => 'boolean',
            'calendar_url' => 'nullable|url|max:255',
            'preferred_contact' => 'nullable|string|max:50',
            'timezone' => 'nullable|string|max:50',
            'working_hours' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Error en los datos enviados')->withErrors($validator)->withInput();
        }

        try {
            $data = [
                'status' => $request->status,
                'response_time' => $request->response_time,
                'custom_message' => $request->custom_message,
                'show_calendar_link' => $request->boolean('show_calendar_link'),
                'calendar_url' => $request->calendar_url,
                'last_updated' => now()->toISOString(),
                'is_active' => true
            ];

            // Construir availability_details JSON
            $availabilityDetails = [];
            if ($request->preferred_contact) {
                $availabilityDetails['preferred_contact'] = $request->preferred_contact;
            }
            if ($request->timezone) {
                $availabilityDetails['timezone'] = $request->timezone;
            }
            if ($request->working_hours) {
                $availabilityDetails['working_hours'] = $request->working_hours;
            }

            if (!empty($availabilityDetails)) {
                $data['availability_details'] = $availabilityDetails;
            }

            $result = $this->supabase->updateAvailability($data);

            if ($result) {
                return redirect()->route('admin.availability.index')
                    ->with('success', 'Estado de disponibilidad actualizado exitosamente');
            } else {
                return back()->with('error', 'Error al actualizar la disponibilidad')->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function quickUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:available,busy,unavailable'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Estado inválido'], 400);
        }

        try {
            $data = [
                'status' => $request->status,
                'last_updated' => now()->toISOString()
            ];

            $result = $this->supabase->updateAvailability($data);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Estado actualizado exitosamente',
                    'status' => $request->status
                ]);
            } else {
                return response()->json(['error' => 'Error al actualizar'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
