<?php

namespace App\Http\Controllers\Admin\Habitaciones;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller {

    /**
     * Display a listing of the resource.
     */
    public function index() {

        $rooms = Room::with('roomtype')->get();
        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        $types = RoomType::all();
        return view('admin.rooms.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $request->validate([
            'room_type_id' => ['required', 'exists:room_types,id', 'unique:rooms'],
            'total_room' => ['required', 'numeric'],
            'no_beds' => ['required', 'numeric'],
            'price' => ['required'],
            'desc' => ['required', 'string'],
            'image' => ['required', 'image', 'max:2048'],
        ], [
            'room_type_id.unique' => 'Este tipo de habitación ya existe en la tabla de habitaciones. Por favor, cree un nuevo tipo de habitación.'
        ]);

        $parsedPrice = $this->parsePriceToNumber($request->price);
        if (!is_numeric($parsedPrice)) {
            return back()->withErrors(['price' => 'Formato de precio inválido. Usa valores como 143.500 o 143.900'])->withInput();
        }
        $imagePath = $request->file('image')->store('habitaciones', 's3');

        $room = Room::create([
            'room_type_id' => $request->room_type_id,
            'total_room' => $request->total_room,
            'no_beds' => $request->no_beds,
            'price' => (float)$parsedPrice,
            'desc' => $request->desc,
            'image' => $imagePath,
            // El estado de bloque no representa mantenimiento individual.
            // Usamos disponible/no disponible para evitar mezclar semánticas.
            'status' => $request->has('status') ? Room::STATUS_DISPONIBLE : Room::STATUS_OCUPADA
        ]);

        registrarAuditoria(
            'CREATE',
            'habitaciones',
            $room->id,
            'Habitación creada: tipo ' . $request->room_type_id . ', total ' . $request->total_room . ' unidades',
            auth()->id()
        );

        return redirect()->route('admin.habitaciones.habitaciones.index')
            ->with('message', 'La habitación ha sido creada!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id) {

        $room = Room::findOrFail($id);
        // $this->authorize('update', $room);
        $types = RoomType::all();
        return view('admin.rooms.edit', compact('room', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id) {

        $request->validate([
            'room_type_id' => ['required', 'exists:room_types,id'],
            'total_room' => ['required', 'numeric'],
            'no_beds' => ['required', 'numeric'],
            'price' => ['required'],
            'desc' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $parsedPrice = $this->parsePriceToNumber($request->price);
        if (!is_numeric($parsedPrice)) {
            return back()->withErrors(['price' => 'Formato de precio inválido. Usa valores como 143.500 o 143.900'])->withInput();
        }

        $room = Room::findOrFail($id);
        $room->room_type_id = $request->room_type_id;
        $room->total_room = $request->total_room;
        $room->no_beds = $request->no_beds;
        $room->price = (float)$parsedPrice;
        $room->desc = $request->desc;
        // El estado de bloque no representa mantenimiento individual.
        $room->status = $request->has('status') ? Room::STATUS_DISPONIBLE : Room::STATUS_OCUPADA;

        if ($request->hasFile('image') && !empty($request->file('image'))) {
            if ($room->image) {
                Storage::disk('s3')->delete($room->image);
            }
            $imagePath = $request->file('image')->store('habitaciones', 's3');
            $room->image = $imagePath;
        }
        $room->save();

        registrarAuditoria(
            'UPDATE',
            'habitaciones',
            $room->id,
            'Habitación actualizada: tipo ' . $request->room_type_id . ', precio ' . $parsedPrice,
            auth()->id()
        );

        return redirect()->route('admin.habitaciones.habitaciones.index')
            ->with('message', 'La habitación ha sido actualizada!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id) {
        abort_unless(auth()->user()->hasRole('administrador'), 403, 'No autorizado para eliminar.');

        $room = Room::findOrFail($id);
        $roomTypeId = $room->room_type_id;
        $room->delete();

        registrarAuditoria(
            'DELETE',
            'habitaciones',
            $id,
            'Habitación eliminada: ID ' . $id . ', tipo ' . $roomTypeId,
            auth()->id()
        );

        return redirect()->route('admin.habitaciones.habitaciones.index')
            ->with('message', 'La habitación ha sido eliminada!');
    }

    private function parsePriceToNumber(string $value): float|int|null
    {
        $raw = trim($value);
        if ($raw === '') return null;

        // Case 1: Proper thousands grouping like 1.200.000 or 168.900
        if (preg_match('/^\d{1,3}(?:\.\d{3})+$/', $raw) === 1) {
            return (int) str_replace('.', '', $raw);
        }

        // Case 2: Single dot with exactly 3 digits (e.g., 168.500)
        if (preg_match('/^\d+\.\d{3}$/', $raw) === 1) {
            return (int) str_replace('.', '', $raw);
        }

        // Remove all non-digits as fallback
        $digitsOnly = preg_replace('/\D+/', '', $raw);
        if ($digitsOnly === '' ) return null;

        // If digits length <= 3, interpret as thousands (e.g., 168 -> 168000)
        if (strlen($digitsOnly) <= 3) {
            return (int)$digitsOnly * 1000;
        }

        // Otherwise, treat as full COP amount (e.g., 168000)
        return (int)$digitsOnly;
    }
}
