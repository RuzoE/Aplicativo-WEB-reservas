<?php

namespace App\Services\Reception;

use App\Models\Guest;
use App\Models\Stay;
use App\Models\Folio;
use App\Models\Room;
use Illuminate\Support\Str;

class WalkInService
{
    /**
     * Procesa un registro directo (Walk-In)
     * 1. Crea o actualiza el huésped
     * 2. Crea la estancia (stay) sin una reserva previa (reservation_id = null)
     * 3. Crea el folio
     */
    public function processWalkIn(array $guestData, int $stayDays): Stay
    {
        $assignedRoomNumber = isset($guestData['assigned_room_number'])
            ? (int) $guestData['assigned_room_number']
            : null;

        $noteTag = $assignedRoomNumber ? '[ROOM_NUM:' . $assignedRoomNumber . ']' : '';
        $freeNotes = trim((string) ($guestData['notes'] ?? ''));
        $notes = trim($noteTag . ' ' . $freeNotes);

        // 1. Crear o buscar el huésped
        $guest = Guest::firstOrCreate(
            ['document_number' => $guestData['document_number'] ?? null],
            [
                'first_name' => $guestData['first_name'],
                'last_name' => $guestData['last_name'],
                'document_type' => $guestData['document_type'] ?? null,
                'email' => $guestData['email'] ?? null,
                'phone' => $guestData['phone'] ?? null,
                'country' => $guestData['country'] ?? null,
            ]
        );

        $room = Room::findOrFail($guestData['room_id']);

        // 2. Crear la estancia (sin Order)
        $stay = Stay::create([
            'reservation_id' => null, // No reservation
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'status' => 'InHouse',
            'arrival_at' => now(), // El cliente acaba de llegar
            'departure_at' => now()->addDays($stayDays), 
            'actual_check_in_at' => now(),
            'adults' => $guestData['adults'] ?? 1,
            'children' => $guestData['children'] ?? 0,
            'daily_rate' => $room->price ?? 0,
            'rate_plan' => 'Walk-In Default',
            'notes' => $notes !== '' ? $notes : null,
        ]);

        // 3. Crear el folio
        $folioNumber = $this->generateFolioNumber();
        Folio::create([
            'stay_id' => $stay->id,
            'number' => $folioNumber,
            'status' => 'Open',
            'currency' => 'COP',
            'balance' => 0,
        ]);

        return $stay;
    }

    /**
     * Genera un número de folio único
     */
    private function generateFolioNumber(): string
    {
        do {
            $number = 'FOL-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Folio::where('number', $number)->exists());

        return $number;
    }
}
