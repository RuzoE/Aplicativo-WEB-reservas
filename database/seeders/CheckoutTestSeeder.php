<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;
use App\Models\Room;
use App\Models\Stay;
use App\Models\Guest;
use App\Models\Folio;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CheckoutTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener habitaciones disponibles
        $rooms = Room::where('status', 'Disponible')->take(3)->get();
        if ($rooms->count() < 3) {
            $this->command->warn('No hay suficientes habitaciones disponibles para el test. Intentando usar cualquiera activa.');
            $rooms = Room::where('status', true)->take(3)->get();
        }

        if ($rooms->count() < 3) {
            $this->command->error('No hay 3 habitaciones activas en el sistema. Asegúrate de tener habitaciones.');
            return;
        }

        $testData = [
            [
                'name' => 'Juan Perez',
                'doc' => '11223344',
                'email' => 'juan.perez@test.com'
            ],
            [
                'name' => 'Maria Gonzalez',
                'doc' => '55667788',
                'email' => 'maria.g@test.com'
            ],
            [
                'name' => 'Carlos Rodriguez',
                'doc' => '99887766',
                'email' => 'carlos.rod@test.com'
            ]
        ];

        foreach ($testData as $index => $data) {
            $room = $rooms[$index];
            $parts = explode(' ', $data['name']);

            // 1. Crear Usuario
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('password'), // password seguro
                    'estado' => 'activo'
                ]
            );

            // 2. Crear Orden (Reserva)
            $order = Order::create([
                'user_id' => $user->id,
                'room_id' => $room->id,
                'check_in' => Carbon::now()->subDays(2),
                'check_out' => Carbon::now()->addDays(1),
                'adults' => 2,
                'children' => 0,
                'status' => 'success',
                'stayDays' => 3
            ]);

            // 3. Crear Huésped (Guest)
            $guest = Guest::firstOrCreate(
                ['document_number' => $data['doc']],
                [
                    'first_name' => collect($parts)->first(),
                    'last_name' => collect($parts)->last() !== collect($parts)->first() ? collect($parts)->last() : '',
                    'document_type' => 'CC',
                    'email' => $data['email'],
                    'phone' => '1234567890',
                    'country' => 'Colombia'
                ]
            );

            // 4. Crear Stay
            $stay = Stay::create([
                'reservation_id' => $order->id,
                'room_id' => $room->id,
                'guest_id' => $guest->id,
                'status' => 'InHouse',
                'arrival_at' => $order->check_in,
                'departure_at' => $order->check_out,
                'actual_check_in_at' => Carbon::now()->subDays(2),
                'adults' => 2,
                'children' => 0,
                'rate_plan' => 'Standard',
                'daily_rate' => $room->price ?? 100,
                'notes' => '[ROOM_NUM:' . $room->total_room . '] Reserva de test para checkout',
            ]);

            // 5. Crear Folio Abierto
            $folio = Folio::create([
                'stay_id' => $stay->id,
                'number' => 'FOL-' . date('Ymd') . '-' . Str::random(4),
                'status' => 'Open',
                'balance' => 0 // Se calculará después, inicialmente 0
            ]);

            // 6. Agregar un cargo de ejemplo para el huésped Juan (index 0) y Carlos (index 2)
            if ($index == 0) {
                // Juan tiene saldo a favor o en 0
                $folio->charges()->create([
                    'folio_id' => $folio->id,
                    'user_id' => 1,
                    'amount' => 150000,
                    'description' => 'Alojamiento',
                    'source' => 'RoomRate'
                ]);
                $folio->payments()->create([
                    'folio_id' => $folio->id,
                    'user_id' => 1,
                    'amount' => 150000,
                    'method' => 'Efectivo',
                    'currency' => 'USD'
                ]);
            } elseif ($index == 1) {
                // Maria tiene todo saldado (o sin cargos)
                 $folio->charges()->create([
                    'folio_id' => $folio->id,
                    'user_id' => 1,
                    'amount' => 250000,
                    'description' => 'Alojamiento',
                    'source' => 'RoomRate'
                ]);
                $folio->payments()->create([
                    'folio_id' => $folio->id,
                    'user_id' => 1,
                    'amount' => 250000,
                    'method' => 'Tarjeta de Crédito',
                    'currency' => 'USD'
                ]);
            } elseif ($index == 2) {
                // Carlos tiene un saldo pendiente
                 $folio->charges()->create([
                    'folio_id' => $folio->id,
                    'user_id' => 1,
                    'amount' => 300000,
                    'description' => 'Alojamiento',
                    'source' => 'RoomRate'
                ]);
                 $folio->charges()->create([
                    'folio_id' => $folio->id,
                    'user_id' => 1,
                    'amount' => 45000,
                    'description' => 'Minibar',
                    'source' => 'POS'
                ]);
                $folio->payments()->create([
                    'folio_id' => $folio->id,
                    'user_id' => 1,
                    'amount' => 300000,
                    'method' => 'Transferencia',
                    'currency' => 'USD'
                ]);
            }

            // Actualizar balance
            $totalCharges = $folio->charges()->sum('amount');
            $totalPayments = $folio->payments()->sum('amount');
            $folio->update(['balance' => $totalCharges - $totalPayments]);

            // 7. Ocupar habitación
            $room->update(['status' => 'reservada']);

            $this->command->info("Huésped activo creado: {$data['name']} (Doc: {$data['doc']}, Hab: {$room->total_room}) | Saldo: " . ($totalCharges - $totalPayments));
        }

        $this->command->info('Se han creado 3 huéspedes activos para pruebas de check-out.');
    }
}
