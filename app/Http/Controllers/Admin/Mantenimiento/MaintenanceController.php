<?php

namespace App\Http\Controllers\Admin\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\MaintenanceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MaintenanceController extends Controller
{
    public function dashboard()
    {
        $roomsCount = (int)Room::sum('total_room');
        $activeOrdersCount = MaintenanceOrder::active()->count();
        $urgentOrdersCount = MaintenanceOrder::urgent()->count();

        return view('admin.mantenimiento.dashboard', compact('roomsCount', 'activeOrdersCount', 'urgentOrdersCount'));
    }

    public function index()
    {
        $roomTypes = Room::with(['roomtype', 'maintenanceOrders'])->get();
        $orders = MaintenanceOrder::with('room')->active()->get();
        $activeCount = MaintenanceOrder::active()->count();
        $urgentCount = MaintenanceOrder::urgent()->count();

        $individualRooms = $this->buildIndividualRooms($roomTypes);
        $this->attachActiveOrders($individualRooms, $orders);

        $totalRooms = $individualRooms->count();

        return view('admin.mantenimiento.index', compact('individualRooms', 'orders', 'activeCount', 'urgentCount', 'totalRooms', 'roomTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'room_number' => 'required|integer|min:1',
            'description' => ['required', 'string', 'not_regex:/^\s*$/'],
            'priority' => 'required|in:baja,normal,urgente',
        ]);

        $individualRooms = $this->buildIndividualRooms(Room::with('roomtype')->get());
        $orders = MaintenanceOrder::active()->get();
        $this->attachActiveOrders($individualRooms, $orders);

        $selectedRoom = $individualRooms
            ->where('room_id', (int) $validated['room_id'])
            ->firstWhere('number', (int) $validated['room_number']);

        if (!$selectedRoom) {
            return back()
                ->withErrors(['room_number' => 'La habitación seleccionada no es válida.'])
                ->withInput();
        }

        if ($selectedRoom->has_active_order) {
            return back()
                ->withErrors(['room_number' => 'La habitación seleccionada ya tiene una orden activa.'])
                ->withInput();
        }

        $order = MaintenanceOrder::create($validated);

        registrarAuditoria(
            'CREATE',
            'mantenimiento',
            $order->id,
            'Orden de mantenimiento creada para room_id ' . $order->room_id . ' (habitacion ' . ($order->room_number ?? 'N/A') . ')',
            auth()->id()
        );

        return redirect()->route('admin.mantenimiento.index')
            ->with('success', 'Orden de mantenimiento creada exitosamente');
    }

    public function markInMaintenance(Room $room)
    {
        // Actualizar estado de la habitación a "mantenimiento"
        // Por ahora usamos el campo status como bandera

        // Crear una orden activa si no existe
        $order = MaintenanceOrder::create([
            'room_id' => $room->id,
            'description' => 'Habitación marcada en mantenimiento',
            'status' => 'asignada',
            'priority' => 'normal',
        ]);

        registrarAuditoria(
            'UPDATE',
            'habitaciones',
            $room->id,
            'Habitacion marcada en mantenimiento mediante orden ID ' . $order->id,
            auth()->id()
        );

        return back()->with('success', 'Habitación ' . $room->total_room . ' marcada en mantenimiento');
    }

    public function completeMaintenanceOrder(MaintenanceOrder $order)
    {
        try {
            $order->update([
                'status' => 'completada',
                'completed_at' => now(),
            ]);

            registrarAuditoria(
                'UPDATE',
                'mantenimiento',
                $order->id,
                'Orden de mantenimiento completada para room_id ' . $order->room_id . ' (habitacion ' . ($order->room_number ?? 'N/A') . ')',
                auth()->id()
            );

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mantenimiento completado',
                    'order_id' => $order->id
                ]);
            }

            return back()->with('success', 'Mantenimiento completado');
        } catch (\Exception $e) {
            \Log::error('Error al completar mantenimiento: ' . $e->getMessage(), [
                'order_id' => $order->id ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No fue posible completar la orden. Intenta nuevamente o contacta al administrador.'
                ], 500);
            }

            return back()->with('error', 'Error al completar el mantenimiento');
        }
    }

    public function markUrgent(MaintenanceOrder $order)
    {
        $order->update(['priority' => 'urgente']);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Orden marcada como urgente']);
        }

        return back()->with('success', 'Orden marcada como urgente');
    }

    public function showHistory(Request $request, Room $room)
    {
        $selectedRoomNumber = $request->integer('room_number');

        $historyQuery = MaintenanceOrder::query()
            ->where('room_id', $room->id);

        // In this project, one room row can represent multiple physical rooms.
        // Keep room_id as primary filter and narrow by room_number when provided.
        if ($selectedRoomNumber > 0) {
            $historyQuery->where('room_number', $selectedRoomNumber);
        }

        $history = $historyQuery
            ->orderBy('created_at', 'desc')
            ->get();

        if (request()->ajax() || request()->wantsJson()) {
            return view('components.admin.mantenimiento.history-list', compact('room', 'history', 'selectedRoomNumber'));
        }

        return view('admin.mantenimiento.history', compact('room', 'history', 'selectedRoomNumber'));
    }

    private function buildIndividualRooms(Collection $roomTypes): Collection
    {
        $individualRooms = collect();
        $roomNumber = 1;

        foreach ($roomTypes as $room) {
            for ($i = 1; $i <= $room->total_room; $i++) {
                $individualRooms->push((object) [
                    'number' => $roomNumber,
                    'room_id' => $room->id,
                    'type_name' => $room->roomtype->name ?? 'Sin tipo',
                    'has_active_order' => false,
                    'active_order' => null,
                ]);
                $roomNumber++;
            }
        }

        return $individualRooms;
    }

    private function attachActiveOrders(Collection $individualRooms, Collection $orders): void
    {
        foreach ($orders as $order) {
            $matching = null;

            if (!is_null($order->room_number)) {
                $matching = $individualRooms
                    ->where('number', (int) $order->room_number)
                    ->first();
            }

            if (!$matching) {
                $matching = $individualRooms
                    ->where('room_id', (int) $order->room_id)
                    ->where('has_active_order', false)
                    ->first();
            }

            if ($matching) {
                $matching->has_active_order = true;
                $matching->active_order = $order;
            }
        }
    }
}
