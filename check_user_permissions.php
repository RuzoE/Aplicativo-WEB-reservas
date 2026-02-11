<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== VERIFICACIÓN DE USUARIOS Y PERMISOS ===\n\n";

$users = User::with('roles', 'permissions')->get();

foreach ($users as $user) {
    echo "User ID: {$user->id}\n";
    echo "Email: {$user->email}\n";
    echo "Name: {$user->name}\n";

    echo "Roles: ";
    if ($user->roles->count() > 0) {
        echo $user->roles->pluck('name')->implode(', ');
    } else {
        echo "NINGUNO";
    }
    echo "\n";

    echo "Permissions: ";
    if ($user->permissions->count() > 0) {
        echo $user->permissions->pluck('name')->implode(', ');
    } else {
        echo "NINGUNO";
    }
    echo "\n";

    echo "hasRole('administrador'): " . ($user->hasRole('administrador') ? 'SÍ' : 'NO') . "\n";
    echo "hasRole('recepcion'): " . ($user->hasRole('recepcion') ? 'SÍ' : 'NO') . "\n";
    echo "hasAnyRole(['administrador', 'recepcion']): " . ($user->hasAnyRole(['administrador', 'recepcion']) ? 'SÍ' : 'NO') . "\n";

    echo str_repeat('-', 60) . "\n";
}
