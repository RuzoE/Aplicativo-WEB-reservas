<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->user()) {
            return $response;
        }

        $module = $request->is('reception*') || $request->is('api/reception*')
            ? 'recepcion'
            : 'usuarios';

        $routeName = $request->route()?->getName() ?? 'sin_nombre';
        $description = 'Acceso administrativo a ruta ' . $routeName;

        registrarAuditoria(
            'ACCESS',
            $module,
            null,
            $description,
            $request->user()->id,
            ['skip_duplicate' => true]
        );

        return $response;
    }
}
