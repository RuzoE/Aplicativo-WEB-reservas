<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Guest;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Stay;
use App\Models\Folio;
use Carbon\Carbon;
use Faker\Factory as Faker;

class FakeStaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        // Ensure there's a Room Type first
        $roomType = \App\Models\RoomType::first();
        if (!$roomType) {
            $roomType = \App\Models\RoomType::create([
                'name' => 'Suite Presidencial Faker',
                'description' => 'Test',
                'price_per_night' => 150000,
                'capacity' => 2,
                'amenities' => json_encode(['Wi-Fi', 'TV']),
                'size' => 45
            ]);
        }

        // Create 5 fake rooms
        for ($i = 0; $i < 5; $i++) {
            Room::create([
                'number' => 'R' . $faker->unique()->numerify('###'),
                'room_type_id' => $roomType->id,
                'status' => 'available',
                'floor' => 1
            ]);
        }

        // Find 5 available rooms
        $rooms = Room::where('status', 'available')->take(5)->get();

        if ($rooms->count() < 5) {
            $this->command->info("Not enough available rooms. Expected 5, found {$rooms->count()}.");
        }

        foreach ($rooms as $room) {
            // 1. Create a Guest
            $guest = Guest::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'document_type' => 'CC',
                'document_number' => $faker->unique()->numerify('##########'),
                'address' => $faker->address,
                'city' => $faker->city,
                'country' => 'Colombia',
            ]);

            // 2. Create a Reservation
            $checkIn = Carbon::now()->subDays(rand(0, 3));
            $checkOut = Carbon::now()->addDays(rand(1, 5));
            $totalPrice = $room->roomType->price_per_night * $checkIn->diffInDays($checkOut);

            $reservation = Reservation::create([
                'guest_id' => $guest->id,
                'room_type_id' => $room->room_type_id,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'status' => 'confirmed',
                'number_of_guests' => rand(1, 2),
                'total_price' => $totalPrice,
            ]);

            // 3. Mark the room as occupied
            $room->update(['status' => 'occupied']);

            // 4. Create the Stay (Check-in)
            $stay = Stay::create([
                'reservation_id' => $reservation->id,
                'room_id' => $room->id,
                'guest_id' => $guest->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'actual_check_in_at' => Carbon::now(),
                'status' => 'InHouse',
                'number_of_guests' => $reservation->number_of_guests,
            ]);

            // 5. Create an Open Folio
            Folio::create([
                'stay_id' => $stay->id,
                'number' => 'FOL-' . str_pad($stay->id, 5, '0', STR_PAD_LEFT),
                'status' => 'Open',
                'currency' => 'COP',
                'balance' => 0,
            ]);

            $this->command->info("Created fake stay for guest: {$guest->name} in room {$room->number}");
        }
    }
}
