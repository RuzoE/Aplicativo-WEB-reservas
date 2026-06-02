<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\UserActivity;
use App\Models\UserSession;

class TrackUserLogout
{
    public function handle(Logout $event): void
    {
        $user = $event->user;
        $request = request();

        if ($user) {
            // Registrar Actividad
            UserActivity::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'activity_type' => 'auth',
                'description' => 'Cierre de sesión',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_name' => $this->getDeviceName($request->userAgent()),
                'status' => 'success',
            ]);

            // Eliminar la sesión actual que coincida
            UserSession::where('user_id', $user->id)
                ->where('ip_address', $request->ip())
                ->where('user_agent', $request->userAgent())
                ->delete();
        }
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
