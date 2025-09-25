<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseServiceOptimized;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $supabase;
    protected $storage;

    public function __construct(SupabaseServiceOptimized $supabase, SupabaseStorageService $storage)
    {
        $this->supabase = $supabase;
        $this->storage = $storage;
    }

    public function index()
    {
        $profile = $this->supabase->getProfile();

        return view('admin.profile.index', compact('profile'));
    }

    public function edit()
    {
        $profile = $this->supabase->getProfile();

        return view('admin.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'title' => 'required|string|max:150',
            'bio' => 'required|string',
            'email' => 'required|email|max:100',
            'location' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'resume_url' => 'nullable|url',
            'skills' => 'nullable|array',
            'avatar' => 'nullable|image|max:2048', // 2MB máximo
        ]);

        $data = [
            'name' => $request->name,
            'title' => $request->title,
            'bio' => $request->bio,
            'email' => $request->email,
            'location' => $request->location,
            'phone' => $request->phone,
            'resume_url' => $request->resume_url,
            'skills' => json_encode($request->skills ?? []),
        ];

        // Manejar subida de avatar si se proporciona
        if ($request->hasFile('avatar')) {
            // Validar imagen adicional
            $imageErrors = SupabaseStorageService::validateImage($request->file('avatar'));

            if (!empty($imageErrors)) {
                return back()->withErrors(['avatar' => implode(' ', $imageErrors)])->withInput();
            }

            // Obtener perfil actual para el ID
            $profile = $this->supabase->getProfile();
            $userId = $profile['id'] ?? uniqid();

            // Subir imagen
            $avatarUrl = $this->storage->uploadProfileImage($request->file('avatar'), $userId);

            if ($avatarUrl) {
                $data['avatar_url'] = $avatarUrl;
            } else {
                return back()->with('error', 'Error al subir la imagen de perfil')->withInput();
            }
        }

        if ($this->supabase->updateProfile($data)) {
            return redirect()->route('admin.profile.index')
                ->with('success', 'Perfil actualizado correctamente');
        }

        return back()->with('error', 'Error al actualizar el perfil');
    }
}
