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

        MaintenanceOrder::create($validated);

        return redirect()->route('admin.mantenimiento.index')
            ->with('success', 'Orden de mantenimiento creada exitosamente');
    }

    public function markInMaintenance(Room $room)
    {
        // Actualizar estado de la habitación a "mantenimiento"
        // Por ahora usamos el campo status como bandera

        // Crear una orden activa si no existe
        MaintenanceOrder::create([
            'room_id' => $room->id,
            'description' => 'Habitación marcada en mantenimiento',
            'status' => 'asignada',
            'priority' => 'normal',
        ]);

        return back()->with('success', 'Habitación ' . $room->total_room . ' marcada en mantenimiento');
    }

    public function completeMaintenanceOrder(MaintenanceOrder $order)
    {
        try {
            $order->update([
                'status' => 'completada',
                'completed_at' => now(),
            ]);

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
                    'message' => 'Error al completar: ' . $e->getMessage()
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

    public function showHistory(Room $room)
    {
        $history = MaintenanceOrder::where('room_id', $room->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.mantenimiento.history', compact('room', 'history'));
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
