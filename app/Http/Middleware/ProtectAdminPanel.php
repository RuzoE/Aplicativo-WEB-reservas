<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectAdminPanel
{
    /**
     * Handle an incoming request.
     * Bloquea acceso al panel administrativo para invitados
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Si es invitado o no es empleado, denegar acceso
            if ($user->hasRole('invitado') || ($user->isGuest() && !$user->isEmployee())) {
                abort(403, 'No tienes permisos para acceder al panel administrativo.');
            }
        }

        return $next($request);
    }
}
