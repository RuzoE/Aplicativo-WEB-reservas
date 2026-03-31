<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class RegistrarLoginAuditoria
{
    public function handle(Login $event): void
    {
        $usuario = $event->user;

        registrarAuditoria(
            'LOGIN',
            'usuarios',
            $usuario?->id,
            'Inicio de sesion exitoso para el usuario ID ' . ($usuario?->id ?? 'N/A'),
            $usuario?->id,
            ['skip_duplicate' => false]
        );
    }
}
