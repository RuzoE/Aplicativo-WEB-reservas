<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\UserActivity;
use App\Models\UserSession;
use Illuminate\Support\Str;

class TrackUserLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        $request = request();

        // Registrar Actividad (Historial de login)
        UserActivity::create([
            'user_id' => $user->id,
            'action' => 'login',
            'activity_type' => 'auth',
            'description' => 'Inicio de sesión exitoso',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_name' => $this->getDeviceName($request->userAgent()),
            'status' => 'success',
        ]);

        // Registrar Sesión Activa
        UserSession::create([
            'user_id' => $user->id,
            'token_name' => session()->getId() ?? Str::random(40),
            'device_name' => $this->getDeviceName($request->userAgent()),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'last_activity_at' => now(),
        ]);
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
