<?php

return [
    'enabled' => env('AUDITORIA_ENABLED', true),

    // Solo registra acciones críticas explícitamente permitidas.
    'allowed_actions' => [
        'CREATE',
        'UPDATE',
        'DELETE',
        'LOGIN',
        'LOGIN_FAILED',
        'ROLE_CHANGE',
        'PASSWORD_CHANGE',
        'ACCESS',
        'CHECK_IN',
        'CHECK_OUT',
        'CANCEL',
    ],

    'allowed_modules' => [
        'reservas',
        'habitaciones',
        'mantenimiento',
        'minibar',
        'usuarios',
        'recepcion',
    ],

    'max_description_length' => env('AUDITORIA_MAX_DESCRIPTION_LENGTH', 255),

    // Evita auditorías idénticas por doble click/envíos repetidos.
    'dedupe_window_seconds' => env('AUDITORIA_DEDUPE_WINDOW_SECONDS', 10),

    'cleanup' => [
        'enabled' => env('AUDITORIA_CLEANUP_ENABLED', true),
        'retention_days' => env('AUDITORIA_RETENTION_DAYS', 90),
        'schedule' => env('AUDITORIA_CLEANUP_SCHEDULE', '03:15'),
    ],
];
