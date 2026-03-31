<?php

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuditoriaService
{
    public function registrar(
        string $accion,
        string $modulo,
        ?int $registroId,
        string $descripcion,
        ?int $usuarioId = null,
        array $context = []
    ): ?Auditoria {
        if (!config('auditoria.enabled', true)) {
            return null;
        }

        $accionNormalizada = strtoupper(trim($accion));
        $moduloNormalizado = Str::lower(trim($modulo));

        if (!$this->esAccionPermitida($accionNormalizada) || !$this->esModuloPermitido($moduloNormalizado)) {
            return null;
        }

        $descripcionLimpia = $this->limpiarDescripcion($descripcion);
        if ($descripcionLimpia === '') {
            return null;
        }

        $usuario = $usuarioId ?? Auth::id() ?? ($context['usuario_id'] ?? null);

        $dedupeWindow = (int) config('auditoria.dedupe_window_seconds', 10);
        $debeEvitarDuplicado = $context['skip_duplicate'] ?? true;

        if ($debeEvitarDuplicado && $dedupeWindow > 0 && $this->esDuplicadaReciente(
            $accionNormalizada,
            $moduloNormalizado,
            $registroId,
            $descripcionLimpia,
            $usuario,
            $dedupeWindow
        )) {
            return null;
        }

        return Auditoria::create([
            'usuario_id' => $usuario,
            'accion' => $accionNormalizada,
            'modulo' => $moduloNormalizado,
            'registro_id' => $registroId,
            'descripcion' => $descripcionLimpia,
            'created_at' => now(),
        ]);
    }

    private function esAccionPermitida(string $accion): bool
    {
        $accionesPermitidas = config('auditoria.allowed_actions', []);

        if (empty($accionesPermitidas)) {
            return true;
        }

        return in_array($accion, $accionesPermitidas, true);
    }

    private function esModuloPermitido(string $modulo): bool
    {
        $modulosPermitidos = config('auditoria.allowed_modules', []);

        if (empty($modulosPermitidos)) {
            return true;
        }

        return in_array($modulo, $modulosPermitidos, true);
    }

    private function limpiarDescripcion(string $descripcion): string
    {
        $textoPlano = strip_tags($descripcion);
        $textoUnificado = preg_replace('/\s+/', ' ', trim($textoPlano)) ?? '';

        return Str::limit($textoUnificado, (int) config('auditoria.max_description_length', 255), '...');
    }

    private function esDuplicadaReciente(
        string $accion,
        string $modulo,
        ?int $registroId,
        string $descripcion,
        ?int $usuarioId,
        int $ventanaSegundos
    ): bool {
        $query = Auditoria::query()
            ->where('accion', $accion)
            ->where('modulo', $modulo)
            ->where('descripcion', $descripcion)
            ->where('created_at', '>=', now()->subSeconds($ventanaSegundos));

        if (is_null($registroId)) {
            $query->whereNull('registro_id');
        } else {
            $query->where('registro_id', $registroId);
        }

        if (is_null($usuarioId)) {
            $query->whereNull('usuario_id');
        } else {
            $query->where('usuario_id', $usuarioId);
        }

        return $query->exists();
    }
}
