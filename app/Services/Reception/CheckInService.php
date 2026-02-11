<?php

namespace App\Services\Reception;

use App\Models\Guest;
use App\Models\Stay;
use App\Models\Folio;
use App\Models\Order;
use Illuminate\Support\Str;

class CheckInService
{
    public function processCheckIn(Order $order, array $guestData): Stay
    {
        // Create or find guest
        $guest = Guest::firstOrCreate(
            ['document_number' => $guestData['document_number'] ?? null],
            $guestData
        );

        // Create stay
        $stay = Stay::create([
            'reservation_id' => $order->id,
            'room_id' => $order->room_id,
            'guest_id' => $guest->id,
            'status' => 'InHouse',
            'arrival_at' => $order->check_in,
            'departure_at' => $order->check_out,
            'actual_check_in_at' => now(),
            'adults' => 1,
            'children' => 0,
            'daily_rate' => 0,
        ]);

        // Open folio
        $folio = Folio::create([
            'stay_id' => $stay->id,
            'number' => (string) Str::uuid(),
            'status' => 'Open',
            'currency' => 'USD',
            'balance' => 0,
        ]);

        // Mark room as occupied
        if ($stay->room) {
            $stay->room->update(['status' => 'occupied']);
        }

        return $stay;
    }

    public function validateCheckIn(Order $order): bool
    {
        // Check if order already has an active stay
        $existingStay = Stay::where('reservation_id', $order->id)
            ->whereIn('status', ['InHouse', 'PreCheckIn'])
            ->exists();

        return !$existingStay;
    }
}
