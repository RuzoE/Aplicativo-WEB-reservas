<?php

namespace App\Http\Controllers\Reception;

use App\Events\Reception\StayStarted;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Rules\AllowedEmailDomain;
use App\Rules\PhoneNumberByPrefix;
use App\Models\Stay;
use App\Services\Reception\WalkInService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WalkInController extends Controller
{
    public function __construct(protected WalkInService $walkInService)
    {
    }

    public function create()
    {
        $this->authorize('create', Stay::class);

        // Fetch all room number options without a specific type filter
        $roomNumberOptions = $this->buildRoomNumberOptions();

        return view('reception.walk_in', compact('roomNumberOptions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Stay::class);

        $data = $request->validate([
            'room_number' => 'required|integer|min:1',
            'stay_days' => 'required|integer|min:1',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'document_type' => 'required|string|max:50|in:CC,CE,PA,NIT,TI',
            'document_number' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:100', new AllowedEmailDomain()],
            'phone' => ['required', new PhoneNumberByPrefix()],
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $roomNumberOptions = $this->buildRoomNumberOptions();
        $selectedNumber = (int) $data['room_number'];
        $selectedOption = $roomNumberOptions->firstWhere('number', $selectedNumber);

        if (!$selectedOption) {
            return back()->withErrors([
                'room_number' => 'El número de habitación seleccionado no existe entre las habitaciones activas.',
            ])->withInput();
        }

        if ($selectedOption->status !== 'Disponible') {
            return back()->withErrors([
                'room_number' => 'La habitación seleccionada no está disponible. Estado actual: ' . $selectedOption->status . '.',
            ])->withInput();
        }

        try {
            $data['assigned_room_number'] = $selectedNumber;
            $data['room_id'] = $selectedOption->room_id;

            $stay = $this->walkInService->processWalkIn($data, (int) $data['stay_days']);
            StayStarted::dispatch($stay);

            return redirect()->route('reception.dashboard')
                ->with(
                    'success',
                    '¡Registro Directo (Walk-In) completado exitosamente! El folio para "' .
                    $data['first_name'] . ' ' . $data['last_name'] .
                    '" (Habitación ' . $selectedNumber .
                    ') ha sido abierto. Folio número: ' . ($stay->folios()->first()->number ?? 'N/A')
                )
                ->with('show_checkin_section', true); // Se redirecciona a la seccin dashboard de reception, podria ser inHouse
        } catch (\Exception $e) {
            Log::error('Error al procesar walk-in.', [
                'user_id' => $request->user()?->id,
                'room_number' => $data['room_number'] ?? null,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'No fue posible completar el registro directo. Intenta nuevamente o contacta al administrador.'])
                ->withInput();
        }
    }

    private function buildRoomNumberOptions(?int $roomTypeId = null): Collection
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

            // Include all rooms
            $options->push((object)[
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
