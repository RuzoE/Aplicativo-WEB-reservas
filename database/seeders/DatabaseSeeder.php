<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Usuarios base
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'admin', 'password' => Hash::make('Password@1'), 'is_admin' => 1]
        );

        $user = User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            ['name' => 'user', 'password' => Hash::make('Password@1')]
        );

        $mantenimiento = User::firstOrCreate(
            ['email' => 'mantenimiento@gmail.com'],
            ['name' => 'mantenimiento', 'password' => Hash::make('Password@1')]
        );

        // 2) Datos de habitaciones
        RoomType::firstOrCreate(['name' => 'Standard']);
        RoomType::firstOrCreate(['name' => 'Deluxe']);
        RoomType::firstOrCreate(['name' => 'Superior']);

        $roomtypes = RoomType::all();
        foreach ($roomtypes as $index => $roomtype) {
            Room::firstOrCreate(
                ['id' => $index + 1],
                [
                    'total_room'   => mt_rand(2, 5),
                    'no_beds'      => mt_rand(1, 4),
                    'price'        => mt_rand(100, 200),
                    'image'        => 'img/room-' . mt_rand(1, 3) . '.jpg',
                    'desc'         => 'Free Coffee',
                    'room_type_id' => $roomtype->id,
                ]
            );
        }

        // 3) Roles y permisos
        $this->call(RoleAndPermissionSeeder::class);

        // 4) Asignación de roles a los usuarios base
        //    (Requiere que el trait HasRoles esté en User.php)
        $admin->syncRoles(['administrador']);  // acceso total
        $user->syncRoles(['invitado']);        // acceso de solo lectura
        $mantenimiento->syncRoles(['mantenimiento']); // acceso a mantenimiento

        // Limpia cache de permisos/roles
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
