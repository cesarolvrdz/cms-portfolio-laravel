<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminCacheMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Solo cache para GET requests
        if ($request->isMethod('GET') && $request->routeIs('admin.*')) {
            // Añadir headers para cache del navegador
            $response->headers->set('Cache-Control', 'public, max-age=60');
            $response->headers->set('Expires', now()->addMinute()->toRfc2822String());
        }

        return $response;
    }
}
