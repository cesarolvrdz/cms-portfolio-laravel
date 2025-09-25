<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el admin está autenticado
        if (!Session::has('admin_authenticated') || Session::get('admin_authenticated') !== true) {
            // Si es una petición AJAX, devolver error 401
            if ($request->ajax()) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            // Redirigir al login con mensaje
            return redirect()
                ->route('login')
                ->with('error', 'Debes iniciar sesión para acceder al panel de administración');
        }

        // Verificar si la sesión no ha expirado (opcional, 8 horas)
        $loginTime = Session::get('admin_login_time');
        if ($loginTime && now()->diffInHours($loginTime) > 8) {
            // Sesión expirada
            Session::forget('admin_authenticated');
            Session::forget('admin_email');
            Session::forget('admin_login_time');

            return redirect()
                ->route('login')
                ->with('error', 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente');
        }

        // Actualizar tiempo de última actividad
        Session::put('admin_last_activity', now());

        return $next($request);
    }
}
