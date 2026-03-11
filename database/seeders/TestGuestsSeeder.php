<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;

class TestGuestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener habitaciones y usuario
        $rooms = Room::limit(3)->get();
        $user = User::whereHas('roles', function ($q) {
            $q->where('name', 'recepcion');
        })->first() ?? User::first();

        $now = Carbon::now();
        $nextWeek = $now->copy()->addDays(7);

        // Crear 3 órdenes pendientes de check-in
        foreach ($rooms as $index => $room) {
            Order::create([
                'room_id' => $room->id,
                'user_id' => $user->id,
                'check_in' => $now->copy()->subHours(2 * ($index + 1)),
                'check_out' => $nextWeek->copy()->addDays($index),
            ]);
        }

        echo "\n✅ Se crearon 3 órdenes pendientes de check-in\n";
    }
}
