<?php

namespace App\Support;

/**
 * Helper estático para transformar logs de auditoría
 * en textos legibles para humanos en la UI.
 *
 * NO modifica los datos en la BD — solo transforma al momento de mostrar.
 */
class AuditoriaHelper
{
    /**
     * Mapa de acciones técnicas a español para mostrar en la UI.
     */
    public const ACCIONES_ES = [
        'ACCESS'     => 'Acceso',
        'CREATE'     => 'Creación',
        'UPDATE'     => 'Actualización',
        'DELETE'     => 'Eliminación',
        'LOGIN'      => 'Inicio de Sesión',
        'LOGIN_FAILED' => 'Inicio Fallido',
        'CHECK_IN'   => 'Ingreso de Huésped',
        'CHECK_OUT'  => 'Salida de Huésped',
        'CANCEL'     => 'Cancelación',
        'ROLE_CHANGE' => 'Cambio de Rol',
        'PASSWORD_CHANGE' => 'Cambio de Contraseña',
    ];

    /**
     * Mapa de módulo a nombre del "elemento" para descripciones CRUD.
     */
    private const ELEMENTO_POR_MODULO = [
        'habitaciones'  => 'habitación',
        'reservas'      => 'reserva',
        'minibar'       => 'producto de minibar',
        'mantenimiento' => 'orden de mantenimiento',
        'usuarios'      => 'empleado',
        'recepcion'     => 'registro de recepción',
        'backups'       => 'backup',
    ];

    /**
     * Mapa de rutas a nombres legibles de módulos para accesos.
     */
    private const RUTAS_LEGIBLES = [
        'admin.index'                      => 'Panel de Administración',
        'admin.minibar.dashboard'          => 'módulo de Minibar',
        'admin.mantenimiento.dashboard'    => 'módulo de Mantenimiento',
        'admin.mantenimiento.index'        => 'módulo de Mantenimiento',
        'admin.habitaciones.dashboard'     => 'módulo de Habitaciones',
        'admin.habitaciones.reservas.index' => 'módulo de Reservas',
        'admin.habitaciones.habitaciones.index' => 'módulo de Habitaciones',
        'admin.habitaciones.tipos-habitacion.index' => 'módulo de Tipos de Habitación',
        'admin.empleados.index'            => 'módulo de Empleados',
        'admin.auditorias.index'           => 'módulo de Auditoría',
        'admin.backups.index'              => 'módulo de Backups',
        'admin.report.preview'             => 'módulo de Informes',
        'reception.dashboard'              => 'módulo de Recepción',
        'admin.minibar.bebida-types.index'  => 'módulo de Tipos de Bebida',
        'admin.minibar.bebida-types-na.index' => 'módulo de Tipos de Bebida No Alcohólica',
        'admin.minibar.bebidas.index'       => 'módulo de Bebidas',
        'admin.minibar.ventas.index'        => 'módulo de Ventas Minibar',
        'reservas.dashboard'               => 'módulo de Reservas',
        'minibarAdmin.dashboard'           => 'módulo de Minibar',
    ];

    /**
     * Traduce la acción técnica a español.
     */
    public static function traducirAccion(string $accion): string
    {
        return self::ACCIONES_ES[strtoupper(trim($accion))] ?? $accion;
    }

    /**
     * Genera una descripción legible para humanos a partir de los datos del log.
     */
    public static function humanizarDescripcion(object $item): string
    {
        $accion = strtoupper(trim((string) $item->accion));
        $modulo = strtolower(trim((string) $item->modulo));
        $nombreUsuario = self::obtenerNombreUsuario($item);
        $registroId = $item->registro_id ?? null;
        $descripcionOriginal = (string) ($item->descripcion ?? '');

        return match ($accion) {
            'LOGIN'       => "El usuario {$nombreUsuario} inició sesión en el sistema",
            'LOGIN_FAILED' => self::humanizarLoginFallido($descripcionOriginal, $nombreUsuario),
            'ACCESS'      => self::humanizarAcceso($descripcionOriginal, $nombreUsuario),
            'CREATE'      => self::humanizarCreate($modulo, $registroId, $nombreUsuario, $descripcionOriginal),
            'UPDATE'      => self::humanizarUpdate($modulo, $registroId, $nombreUsuario, $descripcionOriginal),
            'DELETE'      => self::humanizarDelete($modulo, $registroId, $nombreUsuario, $descripcionOriginal),
            'CHECK_IN'    => self::humanizarCheckIn($descripcionOriginal, $nombreUsuario),
            'CHECK_OUT'   => self::humanizarCheckOut($descripcionOriginal, $nombreUsuario),
            'CANCEL'      => "El usuario {$nombreUsuario} canceló la reserva ID {$registroId}",
            'ROLE_CHANGE' => self::humanizarRoleChange($descripcionOriginal, $nombreUsuario),
            'PASSWORD_CHANGE' => "El usuario {$nombreUsuario} cambió la contraseña del usuario ID {$registroId}",
            default       => $descripcionOriginal,
        };
    }

    private static function obtenerNombreUsuario(object $item): string
    {
        if (isset($item->usuario) && $item->usuario) {
            $nombre = trim(($item->usuario->name ?? '') . ' ' . ($item->usuario->last_name ?? ''));
            return $nombre !== '' ? $nombre : ($item->usuario->email ?? 'Desconocido');
        }
        return 'Sistema';
    }

    private static function humanizarLoginFallido(string $desc, string $nombre): string
    {
        // Extraer email de la descripción original
        if (preg_match('/correo\s+(\S+@\S+)/', $desc, $matches)) {
            return "Intento de inicio de sesión fallido para el correo {$matches[1]}";
        }
        return "Intento de inicio de sesión fallido por {$nombre}";
    }

    private static function humanizarAcceso(string $desc, string $nombre): string
    {
        // Intentar extraer la ruta de la descripción original
        if (preg_match('/ruta\s+([\w.\-]+)/', $desc, $matches)) {
            $ruta = $matches[1];
            $moduloLegible = self::RUTAS_LEGIBLES[$ruta] ?? null;

            if ($moduloLegible) {
                return "El usuario {$nombre} accedió al {$moduloLegible}";
            }

            // Intentar detectar por fragmentos de ruta
            foreach (self::RUTAS_LEGIBLES as $patron => $modLeg) {
                if (str_starts_with($ruta, str_replace('.index', '', $patron))) {
                    return "El usuario {$nombre} accedió al {$modLeg}";
                }
            }

            // Fallback con la ruta limpia
            $rutaLimpia = str_replace(['admin.', '.index', '.dashboard'], ['', '', ''], $ruta);
            return "El usuario {$nombre} accedió al módulo de " . ucfirst($rutaLimpia);
        }

        return "El usuario {$nombre} accedió al sistema";
    }

    private static function humanizarCreate(string $modulo, ?int $registroId, string $nombre, string $desc): string
    {
        $elemento = self::ELEMENTO_POR_MODULO[$modulo] ?? 'registro';
        $moduloLabel = ucfirst($modulo);
        $extra = self::extraerDetalle($desc);

        $texto = "El usuario {$nombre} registró un nuevo {$elemento} en el módulo {$moduloLabel}";
        if ($registroId) {
            $texto .= " (ID {$registroId})";
        }
        if ($extra) {
            $texto .= ". {$extra}";
        }
        return $texto;
    }

    private static function humanizarUpdate(string $modulo, ?int $registroId, string $nombre, string $desc): string
    {
        $elemento = self::ELEMENTO_POR_MODULO[$modulo] ?? 'registro';
        $moduloLabel = ucfirst($modulo);
        $extra = self::extraerDetalle($desc);

        $idPart = $registroId ? " con ID {$registroId}" : '';
        $texto = "El usuario {$nombre} actualizó el {$elemento}{$idPart} en {$moduloLabel}";
        if ($extra) {
            $texto .= ". {$extra}";
        }
        return $texto;
    }

    private static function humanizarDelete(string $modulo, ?int $registroId, string $nombre, string $desc): string
    {
        $elemento = self::ELEMENTO_POR_MODULO[$modulo] ?? 'registro';
        $moduloLabel = ucfirst($modulo);

        $idPart = $registroId ? " con ID {$registroId}" : '';
        return "El usuario {$nombre} eliminó el {$elemento}{$idPart} en {$moduloLabel}";
    }

    private static function humanizarCheckIn(string $desc, string $nombre): string
    {
        // Intentar extraer número de habitación
        if (preg_match('/habitacion\s+(\d+)/i', $desc, $matches)) {
            return "El usuario {$nombre} realizó el ingreso del huésped en habitación {$matches[1]}";
        }
        if (preg_match('/reserva\s+ID\s+(\d+)/i', $desc, $matches)) {
            return "El usuario {$nombre} realizó el ingreso del huésped para la reserva ID {$matches[1]}";
        }
        return "El usuario {$nombre} realizó el ingreso de un huésped";
    }

    private static function humanizarCheckOut(string $desc, string $nombre): string
    {
        // Intentar extraer info
        if (preg_match('/stay\s+ID\s+(\d+)/i', $desc, $matches)) {
            $stayId = $matches[1];
            if (preg_match('/comprobante\s+(\S+)/i', $desc, $compMatches)) {
                return "El usuario {$nombre} realizó la salida del huésped (estancia ID {$stayId}, comprobante {$compMatches[1]})";
            }
            return "El usuario {$nombre} realizó la salida del huésped (estancia ID {$stayId})";
        }
        return "El usuario {$nombre} realizó la salida de un huésped";
    }

    private static function humanizarRoleChange(string $desc, string $nombre): string
    {
        // Extraer el rol asignado
        if (preg_match('/usuario\s+ID\s+(\d+).*?:\s*(\w+)/i', $desc, $matches)) {
            return "El usuario {$nombre} cambió el rol del usuario ID {$matches[1]} a " . ucfirst($matches[2]);
        }
        return "El usuario {$nombre} realizó un cambio de rol";
    }

    /**
     * Extrae un detalle útil de la descripción original para complementar.
     * Por ejemplo: nombre de bebida, tipo de habitación, etc.
     */
    private static function extraerDetalle(string $desc): string
    {
        // Limpiar prefijos comunes como "Bebida creada: Coca Cola, precio 5000"
        $partes = [
            '/^(Bebida\s+\w+):\s*(.+)/i',
            '/^(Tipo\s+de\s+\w+\s*\w*)\s+\w+:\s*(.+)/i',
            '/^(Habitación\s+\w+):\s*(.+)/i',
            '/^(Empleado\s+\w+):\s*(.+)/i',
            '/^(Orden\s+de\s+\w+)\s+\w+\s*.*/i',
        ];

        foreach ($partes as $patron) {
            if (preg_match($patron, $desc, $matches)) {
                return trim($matches[0]);
            }
        }

        return '';
    }
}
