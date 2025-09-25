<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseServiceOptimized $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        try {
            $users = $this->supabase->getCmsUsers();

            // Ordenar por rol y luego por fecha de creación
            usort($users, function($a, $b) {
                $roleOrder = [
                    'super_admin' => 1,
                    'admin' => 2,
                    'editor' => 3,
                    'viewer' => 4
                ];

                $roleA = $roleOrder[$a['role'] ?? 'viewer'] ?? 4;
                $roleB = $roleOrder[$b['role'] ?? 'viewer'] ?? 4;

                if ($roleA == $roleB) {
                    $dateA = strtotime($a['created_at'] ?? '');
                    $dateB = strtotime($b['created_at'] ?? '');
                    return $dateB - $dateA; // Más reciente primero
                }

                return $roleA - $roleB;
            });

            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar los usuarios: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin,editor,viewer',
            'is_active' => 'boolean',
            'avatar_url' => 'nullable|url',
            'two_factor_enabled' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Verificar si el email ya existe
            $existingUser = $this->supabase->getCmsUserByEmail($request->email);
            if ($existingUser) {
                return back()->with('error', 'Ya existe un usuario con este email')->withInput();
            }

            $data = $validator->validated();
            $data['email_verified_at'] = now()->toISOString();

            $result = $this->supabase->createCmsUser($data);

            if ($result) {
                return redirect()->route('admin.users.index')
                    ->with('success', 'Usuario creado exitosamente');
            } else {
                return back()->with('error', 'Error al crear el usuario')->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        try {
            $user = $this->supabase->getCmsUserById($id);

            if (!$user) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Usuario no encontrado');
            }

            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar el usuario: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $user = $this->supabase->getCmsUserById($id);

            if (!$user) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Usuario no encontrado');
            }

            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar el usuario: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = $this->supabase->getCmsUserById($id);

            if (!$user) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Usuario no encontrado');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('cms_users')->ignore($user['email'], 'email')
                ],
                'password' => 'nullable|string|min:8|confirmed',
                'role' => 'required|in:super_admin,admin,editor,viewer',
                'is_active' => 'boolean',
                'avatar_url' => 'nullable|url',
                'two_factor_enabled' => 'boolean'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $data = $validator->validated();

            // Si no hay contraseña nueva, no incluir campos de contraseña
            if (empty($data['password'])) {
                unset($data['password']);
            }

            $result = $this->supabase->updateCmsUser($id, $data);

            if ($result) {
                return redirect()->route('admin.users.index')
                    ->with('success', 'Usuario actualizado exitosamente');
            } else {
                return back()->with('error', 'Error al actualizar el usuario')->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $user = $this->supabase->getCmsUserById($id);

            if (!$user) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Usuario no encontrado');
            }

            // Prevenir eliminación del último super_admin
            if ($user['role'] === 'super_admin') {
                $allUsers = $this->supabase->getCmsUsers();
                $superAdmins = array_filter($allUsers, function($u) {
                    return $u['role'] === 'super_admin';
                });

                if (count($superAdmins) <= 1) {
                    return back()->with('error', 'No se puede eliminar el último Super Administrador');
                }
            }

            $result = $this->supabase->deleteCmsUser($id);

            if ($result) {
                return redirect()->route('admin.users.index')
                    ->with('success', 'Usuario eliminado exitosamente');
            } else {
                return back()->with('error', 'Error al eliminar el usuario');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function activate($id)
    {
        try {
            $result = $this->supabase->activateCmsUser($id);

            if ($result) {
                return back()->with('success', 'Usuario activado exitosamente');
            } else {
                return back()->with('error', 'Error al activar el usuario');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deactivate($id)
    {
        try {
            $user = $this->supabase->getCmsUserById($id);

            if (!$user) {
                return back()->with('error', 'Usuario no encontrado');
            }

            // Prevenir desactivación del último super_admin activo
            if ($user['role'] === 'super_admin') {
                $allUsers = $this->supabase->getCmsUsers();
                $activeSuperAdmins = array_filter($allUsers, function($u) {
                    return $u['role'] === 'super_admin' && $u['is_active'] === true;
                });

                if (count($activeSuperAdmins) <= 1) {
                    return back()->with('error', 'No se puede desactivar el último Super Administrador activo');
                }
            }

            $result = $this->supabase->deactivateCmsUser($id);

            if ($result) {
                return back()->with('success', 'Usuario desactivado exitosamente');
            } else {
                return back()->with('error', 'Error al desactivar el usuario');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
