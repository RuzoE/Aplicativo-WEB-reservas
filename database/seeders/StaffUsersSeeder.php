<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffUsersSeeder extends Seeder
{
    /**
     * Seed staff users and roles requested by the client.
     */
    public function run(): void
    {
        $roles = [
            'administrador',
            'minibar',
            'mantenimiento',
            'reservas',
            'recepcion',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        $users = [
            [
                'name' => 'Administrador General',
                'email' => 'admin@gmail.com',
                'password' => 'Password@1',
                'role' => 'administrador',
            ],
            [
                'name' => 'Carlos',
                'email' => 'carlos@gmail.com',
                'password' => 'Carlos0102.',
                'role' => 'minibar',
            ],
            [
                'name' => 'Kevin',
                'email' => 'kevin@gmail.com',
                'password' => 'Kevin0102.',
                'role' => 'mantenimiento',
            ],
            [
                'name' => 'Josue',
                'email' => 'josue@gmail.com',
                'password' => 'Josue0102.',
                'role' => 'reservas',
            ],
            [
                'name' => 'Mario',
                'email' => 'mario@gmail.com',
                'password' => 'Mario0102.',
                'role' => 'recepcion',
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                ]
            );

            $user->syncRoles([$data['role']]);
        }
    }
}
