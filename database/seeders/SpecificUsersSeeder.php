<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecificUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'last_name' => 'General',
                'email' => 'admin@gmail.com',
                'password' => 'Password@1',
                'role' => 'administrador'
            ],
            [
                'name' => 'Carlos',
                'last_name' => 'Minibar',
                'email' => 'carlos@gmail.com',
                'password' => 'Carlos0102.',
                'role' => 'minibar'
            ],
            [
                'name' => 'Kevin',
                'last_name' => 'Mantenimiento',
                'email' => 'kevin@gmail.com',
                'password' => 'Kevin0102.',
                'role' => 'mantenimiento'
            ],
            [
                'name' => 'Josue',
                'last_name' => 'Reservas',
                'email' => 'josue@gmail.com',
                'password' => 'Josue0102.',
                'role' => 'reservas'
            ],
            [
                'name' => 'Mario',
                'last_name' => 'Recepcion',
                'email' => 'mario@gmail.com',
                'password' => 'Mario0102.',
                'role' => 'recepcion'
            ],
        ];

        foreach ($users as $userData) {
            $user = \App\Models\User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'last_name' => $userData['last_name'],
                    'password' => \Illuminate\Support\Facades\Hash::make($userData['password']),
                ]
            );

            // Ensure role exists (Spatie)
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $userData['role'], 'guard_name' => 'web']);
            
            // Assign role
            $user->syncRoles([$role]);
        }
    }
}
