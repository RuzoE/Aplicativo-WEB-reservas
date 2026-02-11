<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class ReceptionRoleSeeder extends Seeder
{
    public function run()
    {
        $role = Role::firstOrCreate(['name' => 'recepcion']);

        $perms = [
            'reception.view',
            'reception.checkin',
            'reception.folio',
            'reception.checkout',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $role->givePermissionTo($perms);

        // Optional: create a demo recepcion user if not exists (email: recepcion@example.com / password: secret)
        $user = User::firstOrCreate(
            ['email' => 'recepcion@example.com'],
            ['name' => 'Recepcion Demo', 'password' => bcrypt('secret')]
        );

        $user->assignRole($role->name);
    }
}
