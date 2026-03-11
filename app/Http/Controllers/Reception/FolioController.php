<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Stay;
use App\Models\Room;
use App\Models\Guest;
use App\Services\Reception\FolioService;
use App\Events\Reception\ChargePosted;
use App\Events\Reception\PaymentReceived;
use Illuminate\Http\Request;

class FolioController extends Controller
{
    public function __construct(
        protected FolioService $folioService
    ) {}

    public function getActiveGuests()
    {
        $guests = Guest::whereHas('stays', function($query) {
            $query->where('status', 'InHouse');
        })
        ->with(['stays' => function($query) {
            $query->where('status', 'InHouse')->with('room');
        }])
        ->orderBy('first_name')
        ->get()
        ->map(function($guest) {
            return [
                'id' => $guest->id,
                'name' => $guest->first_name . ' ' . $guest->last_name,
                'stay_id' => $guest->stays->first()?->id,
                'room' => $guest->stays->first()?->room?->total_room
            ];
        });

        return response()->json($guests);
    }

    public function search(Request $request)
    {
        $guestId = $request->input('guest_id');
        $roomNumber = $request->input('room_number');
        $queryStr = $request->input('query'); // General search string (for name, document, or room)

        $stayQuery = Stay::where('status', 'InHouse')
            ->with(['folios.charges', 'folios.payments', 'guest', 'room']);

        if ($guestId) {
            $stay = (clone $stayQuery)->where('guest_id', $guestId)->first();
        } elseif ($roomNumber) {
            $room = Room::where('total_room', $roomNumber)->first();
            if ($room) {
                $stay = (clone $stayQuery)->where('room_id', $room->id)->first();
            } else {
                $stay = null;
            }
        } elseif ($queryStr) {
            // Priority 1: Exact room number
            $room = Room::where('total_room', $queryStr)->first();
            if ($room) {
                $stay = clone $stayQuery;
                $stay = $stay->where('room_id', $room->id)->first();
            }

            // Priority 2: Document number
            if (empty($stay)) {
                $stay = clone $stayQuery;
                $stay = $stay->whereHas('guest', function ($q) use ($queryStr) {
                    $q->where('document_number', $queryStr);
                })->first();
            }

            // Priority 3: Name (first or last)
            if (empty($stay)) {
                $stay = clone $stayQuery;
                $stay = $stay->whereHas('guest', function ($q) use ($queryStr) {
                    $q->where('first_name', 'like', "%{$queryStr}%")
                      ->orWhere('last_name', 'like', "%{$queryStr}%")
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$queryStr}%"]);
                })->first();
            }
        } else {
            $stay = null;
        }

        if ($stay) {
            $folio = $stay->folios()->where('status', 'Open')->first();
            return response()->json([
                'success' => true,
                'stay' => $stay,
                'folio' => $folio,
                'charges' => $folio ? $folio->charges : [],
                'payments' => $folio ? $folio->payments : [],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se encontró ningún huésped activo con esos datos.'
        ]);
    }

    public function show($stayId)
    {
        $stay = Stay::with(['folios.charges', 'folios.payments'])->findOrFail($stayId);
        $folio = $stay->folios()->where('status', 'Open')->first();

        if ($folio) {
            $this->authorize('view', $folio);
        }

        return view('reception.folio', compact('stay', 'folio'));
    }

    public function postCharge(Request $request, $stayId)
    {
        try {
            $stay = Stay::with('folios')->findOrFail($stayId);
            $folio = $stay->folios()->where('status', 'Open')->firstOrFail();

            $this->authorize('postCharge', $folio);

            $data = $request->validate([
                'source' => 'required|string',
                'description' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'tax' => 'nullable|numeric|min:0',
            ]);

            $charge = $this->folioService->postCharge($folio, $data, $request->user());

            ChargePosted::dispatch($charge);

            return response()->json([
                'success' => true,
                'message' => 'Cargo agregado exitosamente',
                'charge' => $charge,
                'balance' => $folio->fresh()->balance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar el cargo: ' . $e->getMessage()
            ], 422);
        }
    }

    public function postPayment(Request $request, $stayId)
    {
        try {
            $stay = Stay::with('folios')->findOrFail($stayId);
            $folio = $stay->folios()->where('status', 'Open')->firstOrFail();

            $this->authorize('receivePayment', $folio);

            $data = $request->validate([
                'method' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|size:3',
                'external_ref' => 'nullable|string',
            ]);

            $payment = $this->folioService->receivePayment($folio, $data, $request->user());

            PaymentReceived::dispatch($payment);

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado exitosamente',
                'payment' => $payment,
                'balance' => $folio->fresh()->balance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el pago: ' . $e->getMessage()
            ], 422);
        }
    }
}
