<?php

use App\Models\Auditoria;
use App\Services\AuditoriaService;

if (!function_exists('registrarAuditoria')) {
    function registrarAuditoria(
        string $accion,
        string $modulo,
        ?int $registroId = null,
        string $descripcion = '',
        ?int $usuarioId = null,
        array $context = []
    ): ?Auditoria {
        /** @var AuditoriaService $service */
        $service = app(AuditoriaService::class);

        return $service->registrar(
            $accion,
            $modulo,
            $registroId,
            $descripcion,
            $usuarioId,
            $context
        );
    }
}
