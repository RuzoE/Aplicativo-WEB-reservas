<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia cache de permisos/roles por si re-seedeas
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Permisos por módulo
        $habitaciones = [
            'habitaciones.view',
            'habitaciones.create',
            'habitaciones.update',
            'habitaciones.delete',
            'habitaciones.orders.view',
            'habitaciones.orders.manage',
        ];

        $minibar = [
            'minibar.view',
            'minibar.create',
            'minibar.update',
            'minibar.delete',
            'minibar.ventas.view',
            'minibar.ventas.manage',
        ];

        $mantenimiento = [
            'mantenimiento.view',
            'mantenimiento.create',
            'mantenimiento.update',
            'mantenimiento.delete',
        ];

        // Crear permisos (si no existen)
        $all = array_unique(array_merge($habitaciones, $minibar, $mantenimiento));
        foreach ($all as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Roles del sistema
        $administrador = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $invitado      = Role::firstOrCreate(['name' => 'invitado',      'guard_name' => 'web']);
        // Empleados de módulos específicos
        $reservasRole  = Role::firstOrCreate(['name' => 'reservas',      'guard_name' => 'web']);
        $minibarRole   = Role::firstOrCreate(['name' => 'minibar',       'guard_name' => 'web']);
        $mantenimientoRole = Role::firstOrCreate(['name' => 'mantenimiento', 'guard_name' => 'web']);

        // Asignación de permisos por rol
        // Administrador: acceso total a todo
        $administrador->syncPermissions($all);

        // Invitado: solo puede ver (acceso de lectura)
        $invitado->syncPermissions([
            'habitaciones.view',
            'habitaciones.orders.view',
            'minibar.view',
            'minibar.ventas.view',
            'mantenimiento.view',
        ]);

        // Rol "reservas": acceso completo al módulo de habitaciones/reservas
        $reservasRole->syncPermissions($habitaciones);

        // Rol "minibar": acceso completo al módulo de minibar/ventas
        $minibarRole->syncPermissions($minibar);

        // Rol "mantenimiento": acceso completo al módulo de mantenimiento
        $mantenimientoRole->syncPermissions($mantenimiento);

        // Nota: asignamos roles a usuarios en DatabaseSeeder
    }
}
