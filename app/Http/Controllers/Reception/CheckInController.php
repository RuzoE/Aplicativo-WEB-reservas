<?php

namespace App\Http\Controllers\Reception;

use App\Events\Reception\StayStarted;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Room;
use App\Rules\AllowedEmailDomain;
use App\Rules\PhoneNumberByPrefix;
use App\Models\Stay;
use App\Services\Reception\CheckInService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CheckInController extends Controller
{
    public function __construct(protected CheckInService $checkInService)
    {
    }

    public function search(Request $request)
    {
        // Reservas sin stay activo y con fecha de check-in hoy o pasada.
        $reservations = Order::whereDoesntHave('stays')
            ->whereDate('check_in', '<=', now())
            ->with(['user', 'room.roomtype'])
            ->orderBy('check_in', 'asc')
            ->get();

        if ($reservations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay reservas pendientes de check-in',
            ]);
        }

        return response()->json([
            'success' => true,
            'reservations' => $reservations->map(function ($order) {
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
            }),
        ]);
    }

    public function show($reservationId)
    {
        $reservation = Order::with('room.roomtype')->findOrFail($reservationId);
        $this->authorize('create', Stay::class);

        $reservedRoomTypeId = $reservation->room_type_id;
        $roomNumberOptions = $this->buildRoomNumberOptions($reservedRoomTypeId, $reservationId);

        return view('reception.check_in', compact('reservation', 'roomNumberOptions'));
    }

    public function store(Request $request, $reservationId)
    {
        $this->authorize('create', Stay::class);

        $reservation = Order::with('room')->findOrFail($reservationId);

        $data = $request->validate([
            'room_number' => 'required|integer|min:1',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'document_type' => 'required|string|max:50|in:CC,CE,PA,NIT,TI',
            'document_number' => 'required|string|max:100|unique:guests,document_number',
            'email' => ['required', 'email', 'max:100', new AllowedEmailDomain()],
            'phone' => ['required', new PhoneNumberByPrefix()],
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $reservedRoomTypeId = $reservation->room?->room_type_id;
        $roomNumberOptions = $this->buildRoomNumberOptions($reservedRoomTypeId, $reservationId);
        $selectedNumber = (int) $data['room_number'];
        $selectedOption = $roomNumberOptions->firstWhere('number', $selectedNumber);

        if (!$selectedOption) {
            return back()->withErrors([
                'room_number' => 'El número de habitación seleccionado no existe entre las habitaciones activas.',
            ])->withInput();
        }

        if ($selectedOption['status'] !== 'Disponible') {
            return back()->withErrors([
                'room_number' => 'La habitación seleccionada no está disponible. Estado actual: ' . $selectedOption['status'] . '.',
            ])->withInput();
        }

        if (!$this->checkInService->validateCheckIn($reservation)) {
            return back()->withErrors([
                'reservation' => 'Esta reserva ya tiene un check-in activo.',
            ]);
        }

        try {
            if ((int) $reservation->room_id !== (int) $selectedOption['room_id']) {
                $reservation->update(['room_id' => $selectedOption['room_id']]);
            }

            $data['assigned_room_number'] = $selectedNumber;
            $stay = $this->checkInService->processCheckIn($reservation, $data);
            StayStarted::dispatch($stay);

            return redirect()->route('reception.dashboard')
                ->with(
                    'success',
                    '¡Check-in completado exitosamente! El folio para "' .
                    $data['first_name'] . ' ' . $data['last_name'] .
                    '" (Habitación ' . $selectedNumber .
                    ') ha sido abierto. Folio número: ' . ($stay->folios()->first()->number ?? 'N/A')
                )
                ->with('show_checkin_section', true);
        } catch (\Exception $e) {
            Log::error('Error al procesar check-in.', [
                'reservation_id' => $reservationId,
                'user_id' => $request->user()?->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'No fue posible completar el check-in. Intenta nuevamente o contacta al administrador.'])
                ->withInput();
        }
    }

    private function buildRoomNumberOptions(?int $roomTypeId = null, ?int $currentReservationId = null): Collection
    {
        // La numeración debe ser global (1..N) según todos los bloques activos,
        // y luego se filtra por tipo para conservar el mismo orden que mantenimiento.
        $roomBlocks = Room::with('roomtype')
            ->orderBy('id')
            ->get();

        $numberToRoomId = [];
        $numberToType = [];
        $numberToTypeId = [];
        $roomPricesById = [];
        $roomRanges = [];
        $cursor = 1;

        foreach ($roomBlocks as $roomBlock) {
            $start = $cursor;
            $capacity = max(0, (int) $roomBlock->total_room);
            $roomPricesById[$roomBlock->id] = (float) ($roomBlock->price ?? 0);

            for ($i = 0; $i < $capacity; $i++) {
                $number = $cursor + $i;
                $numberToRoomId[$number] = $roomBlock->id;
                $numberToType[$number] = $roomBlock->roomtype->name ?? 'N/A';
                $numberToTypeId[$number] = (int) $roomBlock->room_type_id;
            }

            if ($capacity > 0) {
                $roomRanges[$roomBlock->id] = [
                    'start' => $start,
                    'end' => $cursor + $capacity - 1,
                ];
            }

            $cursor += $capacity;
        }

        $inHouseStays = Stay::where('status', 'InHouse')->get();
        $activeMaintenance = \App\Models\MaintenanceOrder::active()->get();
        $occupiedNumbers = [];
        $maintenanceNumbers = [];

        // 1) Ocupar números explícitamente guardados en notas.
        foreach ($inHouseStays as $stay) {
            $number = $this->extractAssignedRoomNumber($stay->notes);
            if ($number !== null && isset($numberToRoomId[$number])) {
                $occupiedNumbers[$number] = true;
            }
        }

        // Marcar números en mantenimiento
        foreach ($activeMaintenance as $order) {
            if ($order->room_number && isset($numberToRoomId[$order->room_number])) {
                $maintenanceNumbers[$order->room_number] = true;
            }
        }

        // Marcar números en pre-reserva
        $preReservas = \App\Models\Order::where('status', \App\Models\Order::STATUS_RESERVA_PREVIA)->get();
        $preReservaNumbers = [];
        foreach ($preReservas as $order) {
            if ($currentReservationId !== null && (int)$order->id === (int)$currentReservationId) {
                continue; // Allow the current reservation to be checked in to its pre-reserved room
            }
            if ($order->room_number && isset($numberToRoomId[$order->room_number])) {
                $preReservaNumbers[$order->room_number] = true;
            }
        }

        // 2) Compatibilidad con estancias antiguas sin número explícito: ocupar huecos por bloque.
        foreach ($inHouseStays as $stay) {
            $hasExplicitNumber = $this->extractAssignedRoomNumber($stay->notes) !== null;
            if ($hasExplicitNumber || !isset($roomRanges[$stay->room_id])) {
                continue;
            }

            $range = $roomRanges[$stay->room_id];
            for ($number = $range['start']; $number <= $range['end']; $number++) {
                if (!isset($occupiedNumbers[$number])) {
                    $occupiedNumbers[$number] = true;
                    break;
                }
            }
        }

        $options = collect();
        foreach ($numberToRoomId as $number => $roomId) {
            if ($roomTypeId !== null && ($numberToTypeId[$number] ?? null) !== $roomTypeId) {
                continue;
            }

            $status = 'Disponible';
            if (isset($occupiedNumbers[$number])) {
                $status = 'Ocupada';
            } elseif (isset($maintenanceNumbers[$number])) {
                $status = 'Mantenimiento';
            } elseif (isset($preReservaNumbers[$number])) {
                $status = 'Pre-Reserva';
            }

            // Include all rooms as requested by user
            $options->push([
                'number' => $number,
                'room_id' => $roomId,
                'room_type' => $numberToType[$number] ?? 'N/A',
                'price' => $roomPricesById[$roomId] ?? 0,
                'status' => $status,
            ]);
        }

        return $options;
    }

    private function extractAssignedRoomNumber(?string $notes): ?int
    {
        if (!$notes) {
            return null;
        }

        if (preg_match('/\[ROOM_NUM:(\d+)\]/', $notes, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
