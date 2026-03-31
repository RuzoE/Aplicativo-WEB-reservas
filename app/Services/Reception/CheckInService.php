<?php

namespace App\Services\Reception;

use App\Models\Guest;
use App\Models\Stay;
use App\Models\Folio;
use App\Models\Order;
use Illuminate\Support\Str;

class CheckInService
{
    /**
     * Procesa el check-in de una reserva
     * 1. Crea o actualiza el huésped
     * 2. Crea la estancia (stay)
     * 3. Crea el folio
     * 4. Actualiza estado de la reserva
     */
    public function processCheckIn(Order $order, array $guestData): Stay
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

        // 2. Crear la estancia
        $stay = Stay::create([
            'reservation_id' => $order->id,
            'room_id' => $order->room_id,
            'guest_id' => $guest->id,
            'status' => 'InHouse',
            'arrival_at' => $order->check_in,
            'departure_at' => $order->check_out,
            'actual_check_in_at' => now(),
            'adults' => $guestData['adults'] ?? 1,
            'children' => $guestData['children'] ?? 0,
            'daily_rate' => $order->room->price ?? 0,
            'rate_plan' => 'Standard',
            'notes' => $notes !== '' ? $notes : null,
        ]);

        // 3. Crear el folio
        $folioNumber = $this->generateFolioNumber();
        $folio = Folio::create([
            'stay_id' => $stay->id,
            'number' => $folioNumber,
            'status' => 'Open',
            'currency' => 'COP',
            'balance' => 0,
        ]);

        // 3.1. Registrar anticipo si existe y está pagado
        if ($order->is_paid && $order->down_payment_amount > 0) {
            \App\Models\Payment::create([
                'folio_id' => $folio->id,
                'method' => 'Transferencia/Tarjeta (Anticipo)',
                'amount' => $order->down_payment_amount,
                'currency' => 'COP',
                'received_by' => \Illuminate\Support\Facades\Auth::id() ?? 1, // Fallback to system admin if no auth
                'received_at' => now(),
                'external_ref' => 'ANT-' . $order->payment_token,
                'description' => 'Abono Inicial 30% Reserva',
            ]);

            // Actualizar balance del folio
            $folio->decrement('balance', $order->down_payment_amount);
        }

        // 4. Actualizar el estado de la reserva
        $order->update(['check_in' => now()]);

        registrarAuditoria(
            'CHECK_IN',
            'recepcion',
            $stay->id,
            'Check-in registrado para reserva ID ' . $order->id . ' en habitacion ' . ($assignedRoomNumber ?? 'N/A'),

            \Illuminate\Support\Facades\Auth::id()
        );

        return $stay;
    }

    /**
     * Valida que la reserva pueda ser procesada para check-in
     */
    public function validateCheckIn(Order $order): bool
    {
        // Verificar que no tenga un stay activo
        $existingStay = Stay::where('reservation_id', $order->id)
            ->whereIn('status', ['InHouse', 'PreCheckIn'])
            ->exists();

        return !$existingStay;
    }

    /**
     * Genera un número de folio único
     * Formato: FOL-YYYYMMDD-XXXXXX (donde X es alfanumérico aleatorio)
     */
    private function generateFolioNumber(): string
    {
        do {
            $number = 'FOL-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Folio::where('number', $number)->exists());

        return $number;
    }
}
