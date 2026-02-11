<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Reception\CheckInService;
use App\Events\Reception\StayStarted;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    public function __construct(
        protected CheckInService $checkInService
    ) {}

    public function search(Request $request)
    {
        // Obtener todas las reservas pendientes de check-in
        // (reservas que aún no tienen un Stay asociado y la fecha de check-in es hoy o pasada)
        $reservations = Order::whereDoesntHave('stays')
            ->whereDate('check_in', '<=', now())
            ->with(['user', 'room.roomtype'])
            ->orderBy('check_in', 'asc')
            ->get();

        if ($reservations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay reservas pendientes de check-in'
            ]);
        }

        return response()->json([
            'success' => true,
            'reservations' => $reservations->map(function($order) {
                return [
                    'id' => $order->id,
                    'codigo' => 'RES-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'guest_name' => $order->user->name,
                    'guest_email' => $order->user->email,
                    'room' => $order->room->room_number ?? 'Sin asignar',
                    'room_type' => $order->room->roomtype->name ?? 'N/A',
                    'check_in' => $order->check_in->format('Y-m-d'),
                    'check_out' => $order->check_out->format('Y-m-d'),
                    'total' => number_format($order->stayDays * ($order->room->price ?? 0), 2),
                ];
            })
        ]);
    }

    public function show($reservationId)
    {
        $reservation = Order::findOrFail($reservationId);
        $this->authorize('create', \App\Models\Stay::class);

        return view('reception.check_in', compact('reservation'));
    }

    public function store(Request $request, $reservationId)
    {
        $this->authorize('create', \App\Models\Stay::class);

        $reservation = Order::findOrFail($reservationId);

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'document_type' => 'nullable|string|max:50',
            'document_number' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
        ]);

        if (!$this->checkInService->validateCheckIn($reservation)) {
            return back()->withErrors(['reservation' => 'Esta reserva ya tiene un check-in activo.']);
        }

        $stay = $this->checkInService->processCheckIn($reservation, $data);


        StayStarted::dispatch($stay);

        return redirect()->route('reception.folio.show', ['stay' => $stay->id])
            ->with('status', 'Check-in completado y folio abierto.');
    }
}
