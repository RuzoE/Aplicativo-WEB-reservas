<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Room;
use App\Models\MaintenanceOrder;
use App\Models\Stay;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Mostrar el tablero de habitaciones.
     * Si se pasa un orderId, es la reserva que se está asignando.
     */
    public function index($reservaId = null)
    {
        $roomsCount = Room::all();
        $roomTypes = Room::with(['roomtype'])->get();

        // Unroll rooms sequentially like in Maintenance
        $rooms = $this->buildIndividualRooms($roomTypes);

        // Attach active orders, active stays and maintenance status
        $activeOrders = Order::whereIn('status', [Order::STATUS_RESERVA_PREVIA, Order::STATUS_OCUPADA])->get();
        $activeStays = Stay::where('status', 'InHouse')->get();
        $maintenanceOrders = MaintenanceOrder::active()->get();

        $rooms = $this->attachStatus($rooms, $activeOrders, $activeStays, $maintenanceOrders);

        $selectedOrder = null;
        if ($reservaId) {
            $selectedOrder = Order::with(['roomType', 'user'])->findOrFail($reservaId);
        }

        return view('reception.asignacion', compact('rooms', 'selectedOrder'));
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
                ]);
                $counter++;
            }
        }

        return $individualRooms;
    }

    private function attachStatus($rooms, $activeOrders, $activeStays, $maintenanceOrders)
    {
        return $rooms->map(function ($room) use ($activeOrders, $activeStays, $maintenanceOrders) {
            // Check occupancy (Order)
            $order = $activeOrders->firstWhere('room_number', $room['number']);
            if ($order) {
                if ($order->status === Order::STATUS_RESERVA_PREVIA) {
                    $room['status'] = 'pre-reserva';
                } else {
                    $room['status'] = 'ocupada';
                }
                return $room;
            }

            // Check occupancy (Stay - for walk-ins)
            $stay = $activeStays->firstWhere('assigned_room_number', $room['number']);
            if ($stay) {
                $room['status'] = 'ocupada';
                return $room;
            }

            // Check maintenance
            $maint = $maintenanceOrders->firstWhere('room_number', $room['number']);
            if ($maint) {
                $room['status'] = 'mantenimiento';
                return $room;
            }

            return $room;
        });
    }
}
