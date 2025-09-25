<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseServiceOptimized $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Mostrar el formulario de login
     */
    public function showLoginForm()
    {
        // Si ya está autenticado, redirigir al admin
        if (Session::has('admin_authenticated')) {
            return redirect()->route('admin.projects.index');
        }

        return view('auth.login');
    }

    /**
     * Manejar el intento de login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        try {
            // MÉTODO 1: Intentar con cms_users de Supabase (nuevo sistema)
            try {
                $users = $this->supabaseService->getCmsUsers(false);
                $user = collect($users)->firstWhere('email', $email);

                if ($user) {
                    // Verificar si el usuario está activo
                    if (!($user['is_active'] ?? true)) {
                        return redirect()
                            ->back()
                            ->withInput($request->only('email'))
                            ->withErrors(['login' => 'Esta cuenta ha sido desactivada']);
                    }

                    // Verificar contraseña
                    if (Hash::check($password, $user['password_hash'])) {
                        // Usuario encontrado en cms_users - Actualizar último login
                        $this->supabaseService->updateLastLogin($user['id']);

                        // Crear sesión con datos completos
                        Session::put('admin_authenticated', true);
                        Session::put('admin_user_id', $user['id']);
                        Session::put('admin_email', $user['email']);
                        Session::put('admin_name', $user['name']);
                        Session::put('admin_role', $user['role']);
                        Session::put('admin_login_time', now());

                        return redirect()
                            ->route('admin.projects.index')
                            ->with('success', '¡Bienvenido al panel de administración, ' . $user['name'] . '!');
                    }
                }
            } catch (\Exception $e) {
                \Log::info('cms_users no disponible, usando método de respaldo: ' . $e->getMessage());
            }

            // MÉTODO 2: Sistema de respaldo con credenciales .env (sistema anterior)
            $adminEmail = 'cesolvrdz@gmail.com';
            $adminPassword = 'admin1234';

            if ($email === $adminEmail && $password === $adminPassword) {
                // Crear sesión con datos básicos
                Session::put('admin_authenticated', true);
                Session::put('admin_user_id', 'legacy-user');
                Session::put('admin_email', $email);
                Session::put('admin_name', 'César Olvera Rodríguez');
                Session::put('admin_role', 'super_admin');
                Session::put('admin_login_time', now());

                return redirect()
                    ->route('admin.projects.index')
                    ->with('success', '¡Bienvenido al panel de administración!');
            }

            // Si llegamos aquí, las credenciales son incorrectas
            return redirect()
                ->back()
                ->withInput($request->only('email'))
                ->withErrors(['login' => 'Credenciales incorrectas']);

            // Crear sesión de administrador
            Session::put('admin_authenticated', true);
            Session::put('admin_user_id', $user['id']);
            Session::put('admin_email', $user['email']);
            Session::put('admin_name', $user['name']);
            Session::put('admin_role', $user['role']);
            Session::put('admin_login_time', now());

            return redirect()
                ->route('admin.projects.index')
                ->with('success', '¡Bienvenido al panel de administración, ' . $user['name'] . '!');

        } catch (\Exception $e) {
            \Log::error('Error en login: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput($request->only('email'))
                ->withErrors(['login' => 'Error del sistema. Inténtalo de nuevo.']);
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        // Limpiar sesión de administrador
        Session::forget('admin_authenticated');
        Session::forget('admin_user_id');
        Session::forget('admin_email');
        Session::forget('admin_name');
        Session::forget('admin_role');
        Session::forget('admin_login_time');

        // Limpiar toda la sesión
        Session::flush();

        return redirect()
            ->route('login')
            ->with('success', 'Sesión cerrada correctamente');
    }

    /**
     * Verificar si el usuario está autenticado
     */
    public static function isAuthenticated()
    {
        return Session::has('admin_authenticated') && Session::get('admin_authenticated') === true;
    }

    /**
     * Obtener información del admin autenticado
     */
    public static function getAuthenticatedAdmin()
    {
        if (!self::isAuthenticated()) {
            return null;
        }

        return [
            'id' => Session::get('admin_user_id'),
            'email' => Session::get('admin_email'),
            'name' => Session::get('admin_name'),
            'role' => Session::get('admin_role'),
            'login_time' => Session::get('admin_login_time'),
        ];
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public static function hasRole($role)
    {
        if (!self::isAuthenticated()) {
            return false;
        }

        $userRole = Session::get('admin_role');

        // Super admin tiene acceso a todo
        if ($userRole === 'super_admin') {
            return true;
        }

        return $userRole === $role;
    }

    /**
     * Verificar si el usuario tiene al menos un nivel de rol
     */
    public static function hasMinimumRole($role)
    {
        if (!self::isAuthenticated()) {
            return false;
        }

        $userRole = Session::get('admin_role');
        $roleHierarchy = ['viewer', 'editor', 'admin', 'super_admin'];

        $userLevel = array_search($userRole, $roleHierarchy);
        $requiredLevel = array_search($role, $roleHierarchy);

        return $userLevel !== false && $requiredLevel !== false && $userLevel >= $requiredLevel;
    }
}
