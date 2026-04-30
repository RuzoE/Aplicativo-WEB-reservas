<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Room;
use App\Models\MaintenanceOrder;
use App\Models\Stay;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Mostrar el tablero de habitaciones.
     * Si se pasa un orderId, es la reserva que se está asignando.
     */
    public function index($reservaId = null)
    {
        $roomTypes = Room::with(['roomtype'])->get();
        $rooms = $this->buildIndividualRooms($roomTypes);

        // Estado para la fecha de hoy
        $today = Carbon::today();
        $rooms = $this->attachStatusByDate($rooms, $today);

        $selectedOrder = null;
        if ($reservaId) {
            $selectedOrder = Order::with(['roomType', 'user'])->findOrFail($reservaId);
        }

        return view('reception.asignacion', compact('rooms', 'selectedOrder'));
    }

    /**
     * API AJAX: devuelve el estado de habitaciones para una fecha específica.
     */
    public function roomsByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date|date_format:Y-m-d',
        ]);

        $date = Carbon::parse($request->input('date'));

        $roomTypes = Room::with(['roomtype'])->get();
        $rooms = $this->buildIndividualRooms($roomTypes);
        $rooms = $this->attachStatusByDate($rooms, $date);

        return response()->json([
            'rooms' => $rooms->values()->toArray(),
            'date' => $date->toDateString(),
        ]);
    }

    /**
     * Confirmar la asignación de habitación a una reserva.
     */
    public function assign(Request $request, Order $reserva, Room $room)
    {
        $roomNumber = (string) $request->room_number;

        // Se espera room_number en el request desde el componente Vue
        $reserva->update([
            'room_id' => $room->id,
            'room_number' => $roomNumber,
            'status' => Order::STATUS_RESERVA_PREVIA
        ]);

        registrarAuditoria(
            'UPDATE',
            'habitaciones',
            $room->id,
            'Habitacion ' . $roomNumber . ' asignada a la reserva ID ' . $reserva->id,
            auth()->id()
        );

        return redirect()->route('reception.asignacion.index')
            ->with('success', "Habitación {$request->room_number} asignada correctamente a {$reserva->nombre_cliente}");
    }

    /**
     * Construye la lista de habitaciones individuales a partir de los bloques de Room.
     */
    private function buildIndividualRooms($roomTypes)
    {
        $individualRooms = collect();
        $counter = 1;

        foreach ($roomTypes as $room) {
            for ($i = 1; $i <= $room->total_room; $i++) {
                $individualRooms->push([
                    'id' => $room->id,
                    'number' => (string)$counter,
                    'type_name' => $room->roomtype->name ?? 'Estándar',
                    'status' => 'disponible',
                    'guest_name' => null,
                    'order_info' => null,
                ]);
                $counter++;
            }
        }

        return $individualRooms;
    }

    /**
     * Determina el estado de cada habitación para una fecha específica.
     *
     * Prioridad:
     *   1. Mantenimiento activo (no depende de fecha)
     *   2. Stay con status='InHouse' cuyo rango cubre la fecha → ROJO (ocupada)
     *   3. Order con check_in == fecha y room_number asignado → AZUL (pre-reserva)
     *   4. De lo contrario → VERDE (disponible)
     */
    private function attachStatusByDate($rooms, Carbon $date)
    {
        // 1. Mantenimiento activo (no depende de fecha)
        $maintenanceOrders = MaintenanceOrder::active()->get();

        // 2. Stays activos cuyo rango de fechas incluye la fecha seleccionada
        $activeStays = Stay::where('status', 'InHouse')
            ->whereDate('actual_check_in_at', '<=', $date)
            ->whereDate('departure_at', '>=', $date)
            ->get();

        // 3. Pre-reservas: Órdenes con room_number asignado cuya check_in es EXACTAMENTE esa fecha
        //    Incluye status pendiente, anticipo_pagado y reserva_previa
        $preReservas = Order::whereIn('status', [
                Order::STATUS_PENDIENTE,
                Order::STATUS_ANTICIPO_PAGADO,
                Order::STATUS_RESERVA_PREVIA,
            ])
            ->whereDate('check_in', $date)
            ->whereNotNull('room_number')
            ->get();

        return $rooms->map(function ($room) use ($maintenanceOrders, $activeStays, $preReservas) {
            $roomNumber = $room['number'];

            // 1. Mantenimiento activo
            $maint = $maintenanceOrders->firstWhere('room_number', $roomNumber);
            if ($maint) {
                $room['status'] = 'mantenimiento';
                return $room;
            }

            // 2. Ocupada (Stay InHouse que cubre la fecha)
            $stay = $activeStays->first(function ($s) use ($roomNumber) {
                // Extraer room number desde notas
                $assignedNumber = null;
                if ($s->notes && preg_match('/\[ROOM_NUM:(\d+)\]/', $s->notes, $matches)) {
                    $assignedNumber = $matches[1];
                }
                return (string) $assignedNumber === (string) $roomNumber;
            });

            if ($stay) {
                $room['status'] = 'ocupada';
                $guestName = null;
                if ($stay->guest) {
                    $guestName = trim(($stay->guest->first_name ?? '') . ' ' . ($stay->guest->last_name ?? ''));
                }
                $room['guest_name'] = $guestName ?: null;
                $room['order_info'] = [
                    'stay_id' => $stay->id,
                    'check_in' => optional($stay->actual_check_in_at)->format('d/m/Y'),
                    'check_out' => optional($stay->departure_at)->format('d/m/Y'),
                    'guest_name' => $guestName,
                ];
                return $room;
            }

            // 3. Pre-reserva (check_in exactamente en la fecha seleccionada)
            $order = $preReservas->firstWhere('room_number', $roomNumber);
            if ($order) {
                $room['status'] = 'pre-reserva';
                $room['guest_name'] = $order->nombre_cliente ?? $order->user?->name ?? null;
                $room['order_info'] = [
                    'order_id' => $order->id,
                    'guest_name' => $room['guest_name'],
                    'check_in' => optional($order->check_in)->format('d/m/Y'),
                    'check_out' => optional($order->check_out)->format('d/m/Y'),
                    'room_type' => $order->roomType?->name ?? 'N/A',
                    'status' => $order->status,
                    'total' => $order->total_amount,
                ];
                return $room;
            }

            // 4. Disponible
            $room['status'] = 'disponible';
            return $room;
        });
    }
}
