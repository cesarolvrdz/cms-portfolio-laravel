<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\Request;

class SocialLinksController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseServiceOptimized $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        $socialLinks = $this->supabase->getSocialLinks(false); // Obtener todos, activos e inactivos

        return view('admin.social.index', compact('socialLinks'));
    }

    public function create()
    {
        return view('admin.social.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'platform' => 'required|string|max:50',
            'url' => 'required|url',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer',
        ]);

        // Verificar manualmente si ya existe la plataforma
        $existingLinks = $this->supabase->getSocialLinks(false);
        $platformExists = collect($existingLinks)->contains('platform', $request->platform);

        if ($platformExists) {
            return back()->withErrors(['platform' => 'Ya existe un enlace para esta plataforma.'])
                ->withInput();
        }

        $data = [
            'platform' => $request->platform,
            'url' => $request->url,
            'icon' => $request->icon ?? 'bi-link',
            'color' => $request->color ?? '#666666',
            'order' => $request->order ?? 0,
            'is_active' => true,
        ];

        if ($this->supabase->createSocialLink($data)) {
            return redirect()->route('admin.social.index')
                ->with('success', 'Enlace social creado correctamente');
        }

        return back()->with('error', 'Error al crear el enlace social');
    }

    public function edit($id)
    {
        $socialLink = $this->supabase->getSocialLink($id);

        if (!$socialLink) {
            return redirect()->route('admin.social.index')
                ->with('error', 'Enlace social no encontrado');
        }

        return view('admin.social.edit', compact('socialLink'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'platform' => 'required|string|max:50',
            'url' => 'required|url',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        // Verificar manualmente si ya existe la plataforma (excluyendo el actual)
        $existingLinks = $this->supabase->getSocialLinks(false);
        $platformExists = collect($existingLinks)
            ->where('platform', $request->platform)
            ->where('id', '!=', $id)
            ->isNotEmpty();

        if ($platformExists) {
            return back()->withErrors(['platform' => 'Ya existe un enlace para esta plataforma.'])
                ->withInput();
        }

        $data = [
            'platform' => $request->platform,
            'url' => $request->url,
            'icon' => $request->icon ?? 'bi-link',
            'color' => $request->color ?? '#666666',
            'order' => $request->order ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($this->supabase->updateSocialLink($id, $data)) {
            return redirect()->route('admin.social.index')
                ->with('success', 'Enlace social actualizado correctamente');
        }

        return back()->with('error', 'Error al actualizar el enlace social');
    }

    public function destroy($id)
    {
        if ($this->supabase->deleteSocialLink($id)) {
            return redirect()->route('admin.social.index')
                ->with('success', 'Enlace social eliminado correctamente');
        }

        return back()->with('error', 'Error al eliminar el enlace social');
    }
}
