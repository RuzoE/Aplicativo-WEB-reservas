<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class TestGuestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Crea 5 reservas pendientes de check-in (Orders sin Stay asociado
     * y con check_in <= hoy) para probar el flujo de recepción.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Datos de los 5 clientes demo
        $clientes = [
            ['name' => 'Carlos Ramírez',    'email' => 'carlos.ramirez@demo.com',    'days_out' => 3],
            ['name' => 'María González',    'email' => 'maria.gonzalez@demo.com',    'days_out' => 2],
            ['name' => 'Andrés Herrera',    'email' => 'andres.herrera@demo.com',    'days_out' => 4],
            ['name' => 'Lucía Martínez',    'email' => 'lucia.martinez@demo.com',    'days_out' => 5],
            ['name' => 'Jorge Medina',      'email' => 'jorge.medina@demo.com',      'days_out' => 2],
        ];

        // Habitaciones activas disponibles
        $rooms = Room::where('status', true)->get();

        if ($rooms->isEmpty()) {
            $rooms = Room::all();
        }

        if ($rooms->isEmpty()) {
            $this->command->error('No hay habitaciones en la base de datos. Crea habitaciones primero.');
            return;
        }

        $created = 0;

        foreach ($clientes as $index => $cliente) {
            // Reutilizar o crear el usuario del cliente
            $user = User::firstOrCreate(
                ['email' => $cliente['email']],
                [
                    'name'     => $cliente['name'],
                    'password' => Hash::make('password'),
                ]
            );

            // Asignar habitación cíclicamente
            $room = $rooms[$index % $rooms->count()];

            // Verificar que no exista ya una orden pendiente para este usuario/habitación
            $existe = Order::where('user_id', $user->id)
                ->where('room_id', $room->id)
                ->whereDoesntHave('stays')
                ->whereDate('check_in', '<=', $now->toDateString())
                ->exists();

            if ($existe) {
                $this->command->line("⚠️  Ya existe reserva pendiente para {$cliente['name']}, omitiendo.");
                continue;
            }

            Order::create([
                'room_id'  => $room->id,
                'user_id'  => $user->id,
                'check_in' => $now->copy()->subHours($index + 1),         // check-in ya vencido (hoy)
                'check_out' => $now->copy()->addDays($cliente['days_out']),
            ]);

            $created++;
            $this->command->line("✅ Reserva creada para {$cliente['name']} → Habitación ID {$room->id}");
        }

        $this->command->info("\n{$created} reserva(s) pendientes de check-in creadas.");
    }
}
