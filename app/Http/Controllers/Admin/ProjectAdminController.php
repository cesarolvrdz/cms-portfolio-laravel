<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseServiceOptimized as SupabaseService;
use Illuminate\Http\Request;

class ProjectAdminController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index(Request $request)
    {
        $projects = $this->supabase->getProjects();
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        // Validación básica
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:completed,in-progress,planned',
        ]);

        $data = [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'link' => $request->link,
            'tech' => $request->tech ? array_map('trim', explode(',', $request->tech)) : [],
            'image' => $request->image ? 'https://via.placeholder.com/600x400.png?text=Proyecto' : 'https://via.placeholder.com/600x400.png?text=Proyecto',
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ];

        try {
            $result = $this->supabase->createProject($data);

            if ($result) {
                return redirect()->route('admin.projects.index')->with('success', 'Proyecto creado exitosamente');
            } else {
                return redirect()->back()->with('error', 'Error al crear el proyecto')->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $projects = $this->supabase->getProjects(['id' => 'eq.' . $id]);
        if (empty($projects)) {
            return redirect()->route('admin.projects.index')->with('error', 'Proyecto no encontrado');
        }
        $project = $projects[0]; // Tomar el primer (y único) resultado
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, $id)
    {
        // Validación
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:completed,in-progress,planned',
            'link' => 'nullable|url',
            'tech' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'link' => $request->link,
            'tech' => $request->tech ? json_decode(json_encode(array_map('trim', explode(',', $request->tech)))) : [],
            'updated_at' => now()->toISOString(),
        ];

        // Subir nueva imagen si se proporciona
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = $id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $url = $this->supabase->uploadImage($image, $filename);
            if ($url) {
                $data['image'] = $url;
            } else {
                return redirect()->back()->withErrors(['image' => 'Error al subir la imagen'])->withInput();
            }
        }

        try {
            $result = $this->supabase->updateProject($id, $data);

            // Forzar limpieza de cache
            \Illuminate\Support\Facades\Cache::flush();

            // Verificar que se actualizó correctamente
            sleep(1);
            $updated = $this->supabase->getProjects(['id' => 'eq.' . $id], false);

            if (!empty($updated) && $updated[0]['title'] === $data['title']) {
                return redirect()->route('admin.projects.index')
                    ->with('success', 'Proyecto actualizado exitosamente');
            } else {
                return redirect()->back()
                    ->with('error', 'No se pudo actualizar el proyecto. Verifica los permisos.')
                    ->withInput();
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->supabase->deleteProject($id);

            // Forzar limpieza de cache
            \Illuminate\Support\Facades\Cache::flush();

            // Verificar que se eliminó correctamente
            sleep(1);

            // Verificar que el proyecto ya no existe
            $check = $this->supabase->getProjects(['id' => 'eq.' . $id], false); // Sin cache

            if (empty($check)) {
                return redirect()->route('admin.projects.index')
                    ->with('success', 'Proyecto eliminado exitosamente');
            } else {
                return redirect()->route('admin.projects.index')
                    ->with('error', 'No se pudo eliminar el proyecto. Verifica los permisos.');
            }

        } catch (\Exception $e) {
            return redirect()->route('admin.projects.index')
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}
