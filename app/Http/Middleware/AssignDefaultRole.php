<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role;

class AssignDefaultRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está autenticado y no tiene rol, asignarle 'invitado'
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user->roles()->count() === 0) {
                $guestRole = Role::firstOrCreate(
                    ['name' => 'invitado', 'guard_name' => 'web']
                );
                $user->assignRole($guestRole);
                $user->update(['is_employee' => false]);
            }
        }

        return $next($request);
    }
}
