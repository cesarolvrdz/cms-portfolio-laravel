<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\Request;

class SiteSettingsController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseServiceOptimized $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        $settings = $this->supabase->getSiteSettings();

        // Agrupar configuraciones por grupo
        $groupedSettings = collect($settings)->groupBy('group');

        return view('admin.settings.index', compact('groupedSettings'));
    }

    public function show($group = null)
    {
        $settings = $this->supabase->getSiteSettings($group);

        return view('admin.settings.group', compact('settings', 'group'));
    }

    public function update(Request $request, $key)
    {
        $request->validate([
            'value' => 'required|string',
        ]);

        if ($this->supabase->updateSiteSetting($key, $request->value)) {
            return back()->with('success', 'Configuración actualizada correctamente');
        }

        return back()->with('error', 'Error al actualizar la configuración');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:100|unique:site_settings,key',
            'value' => 'required|string',
            'type' => 'required|in:text,textarea,number,boolean,email,url,date,json',
            'group' => 'required|string|max:50',
            'label' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $data = [
            'key' => $request->key,
            'value' => $request->value,
            'type' => $request->type,
            'group' => $request->group,
            'label' => $request->label,
            'description' => $request->description,
            'is_public' => $request->boolean('is_public'),
        ];

        if ($this->supabase->createSiteSetting($data)) {
            return redirect()->route('admin.settings.index')
                ->with('success', 'Configuración creada correctamente');
        }

        return back()->with('error', 'Error al crear la configuración');
    }

    public function destroy($id)
    {
        if ($this->supabase->deleteSiteSetting($id)) {
            return redirect()->route('admin.settings.index')
                ->with('success', 'Configuración eliminada correctamente');
        }

        return back()->with('error', 'Error al eliminar la configuración');
    }
}
