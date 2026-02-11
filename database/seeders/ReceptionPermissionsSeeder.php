<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ReceptionPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Define permissions
        $permissions = [
            'reception.access',
            'reception.checkin',
            'reception.checkout',
            'reception.folio.view',
            'reception.folio.post_charge',
            'reception.folio.receive_payment',
            'reception.room_move',
            'reception.keycard.manage',
            'reception.incident.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create receptionist role
        $receptionist = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
        $receptionist->syncPermissions([
            'reception.access',
            'reception.checkin',
            'reception.checkout',
            'reception.folio.view',
            'reception.folio.post_charge',
            'reception.folio.receive_payment',
        ]);

        // Create frontdesk_manager role
        $manager = Role::firstOrCreate(['name' => 'frontdesk_manager', 'guard_name' => 'web']);
        $manager->syncPermissions($permissions);

        $this->command->info('Reception roles and permissions seeded successfully.');
    }
}
