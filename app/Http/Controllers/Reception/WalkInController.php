<?php

namespace App\Http\Controllers\Reception;

use App\Events\Reception\StayStarted;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Rules\AllowedEmailDomain;
use App\Rules\PhoneNumberByPrefix;
use App\Models\Stay;
use App\Services\Reception\WalkInService;
use Carbon\Carbon;
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
        $availability = $this->buildRoomNumberOptions(1);
        $roomNumberOptions = $availability['available'];
        $unavailableOptions = $availability['unavailable'];

        return view('reception.walk_in', compact('roomNumberOptions', 'unavailableOptions'));
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

        $stayDays = (int) $data['stay_days'];
        $availability = $this->buildRoomNumberOptions($stayDays);
        $selectedNumber = (int) $data['room_number'];
        
        $selectedOption = null;
        foreach ($availability['available'] as $opt) {
            if ($opt->number === $selectedNumber) {
                $selectedOption = $opt;
                break;
            }
        }

        if (!$selectedOption) {
            return back()->withErrors([
                'room_number' => 'La habitación seleccionada no está disponible para los días de estadía ingresados.',
            ])->withInput();
        }

        try {
            $data['assigned_room_number'] = $selectedNumber;
            $data['room_id'] = $selectedOption->room_id;

            $stay = $this->walkInService->processWalkIn($data, $stayDays);
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

    public function checkAvailability(Request $request)
    {
        $days = (int) $request->input('stay_days', 1);
        if ($days < 1) $days = 1;

        $availability = $this->buildRoomNumberOptions($days);
        return response()->json($availability);
    }

    private function buildRoomNumberOptions(int $stayDays = 1, ?int $roomTypeId = null): array
    {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays($stayDays);

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

        $activeMaintenance = \App\Models\MaintenanceOrder::active()->get();
        
        // Stays que terminan DESPUÉS de hoy (la estadía actual se solapa)
        $inHouseStays = Stay::where('status', 'InHouse')
            ->whereDate('departure_at', '>', $startDate)
            ->get();
            
        // Órdenes que inician ANTES del fin del walk-in y no están canceladas
        // Solo futuras o presentes (check_in >= startDate)
        $preReservas = \App\Models\Order::whereIn('status', [
                \App\Models\Order::STATUS_PENDIENTE,
                \App\Models\Order::STATUS_ANTICIPO_PAGADO,
                \App\Models\Order::STATUS_RESERVA_PREVIA
            ])
            ->whereDate('check_in', '<', $endDate)
            ->whereDate('check_in', '>=', $startDate)
            ->whereNotNull('room_number')
            ->get();

        $unavailableRooms = []; // number => message

        // Marcar números en mantenimiento
        foreach ($activeMaintenance as $order) {
            if ($order->room_number && isset($numberToRoomId[$order->room_number])) {
                $unavailableRooms[$order->room_number] = 'En mantenimiento';
            }
        }

        // Marcar números ocupados (Stays)
        foreach ($inHouseStays as $stay) {
            $number = $this->extractAssignedRoomNumber($stay->notes);
            if ($number !== null && isset($numberToRoomId[$number])) {
                $unavailableRooms[$number] = 'Ocupada hasta ' . optional($stay->departure_at)->translatedFormat('d M');
            }
        }

        // Marcar números en pre-reserva
        foreach ($preReservas as $order) {
            if ($order->room_number && isset($numberToRoomId[$order->room_number])) {
                $unavailableRooms[$order->room_number] = 'Reserva el ' . optional($order->check_in)->translatedFormat('d M');
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
                if (!isset($unavailableRooms[$number])) {
                    $unavailableRooms[$number] = 'Ocupada hasta ' . optional($stay->departure_at)->translatedFormat('d M');
                    break;
                }
            }
        }

        $available = [];
        $unavailable = [];

        foreach ($numberToRoomId as $number => $roomId) {
            if ($roomTypeId !== null && ($numberToTypeId[$number] ?? null) !== $roomTypeId) {
                continue;
            }

            $roomData = (object)[
                'number' => $number,
                'room_id' => $roomId,
                'room_type' => $numberToType[$number] ?? 'N/A',
                'price' => $roomPricesById[$roomId] ?? 0,
                'status' => 'Disponible'
            ];

            if (isset($unavailableRooms[$number])) {
                $roomData->reason = $unavailableRooms[$number];
                $unavailable[] = $roomData;
            } else {
                $available[] = $roomData;
            }
        }

        return [
            'available' => $available,
            'unavailable' => $unavailable
        ];
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
