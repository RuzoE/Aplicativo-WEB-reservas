<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserSession;
use Illuminate\Support\Str;

class UpdateUserSessionActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $session = UserSession::where('user_id', auth()->id())
                ->where('ip_address', $request->ip())
                ->where('user_agent', $request->userAgent())
                ->first();

            if ($session) {
                // Actualizar solo si pasaron más de 1 minuto desde el último registro para ahorrar consultas DB
                if ($session->last_activity_at->diffInMinutes(now()) >= 1) {
                    $session->update(['last_activity_at' => now()]);
                }
            } else {
                UserSession::create([
                    'user_id' => auth()->id(),
                    'token_name' => session()->getId() ?? Str::random(40),
                    'device_name' => $this->getDeviceName($request->userAgent()),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'last_activity_at' => now(),
                ]);
            }
        }

        return $next($request);
    }

    private function getDeviceName(?string $ua): string
    {
        if (!$ua) return 'Desconocido';
        if (str_contains($ua, 'Windows')) return 'Windows';
        if (str_contains($ua, 'Android')) return 'Android';
        if (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS')) return 'macOS';
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) return 'iOS';
        if (str_contains($ua, 'Linux')) return 'Linux';
        return 'Desconocido';
    }
}
