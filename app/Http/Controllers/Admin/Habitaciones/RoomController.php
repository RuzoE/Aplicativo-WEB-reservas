<?php

namespace App\Http\Controllers\Admin\Habitaciones;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

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
        $imageName = time() . '.' . $request->file('image')->extension();

        // download image
        $request->file('image')->move(public_path('img'), $imageName);
        $imagePath = 'img/' . $imageName;

        Room::create([
            'room_type_id' => $request->room_type_id,
            'total_room' => $request->total_room,
            'no_beds' => $request->no_beds,
            'price' => (float)$parsedPrice,
            'desc' => $request->desc,
            'image' => $imagePath,
            'status' => $request->has('status') ? 1 : 0
        ]);

        return redirect()->route('admin.habitaciones.rooms.index')
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
        $room->status = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image') && !empty($request->file('image'))) {
            $imageName = time() . '.' . $request->file('image')->extension();

            // download image
            $request->file('image')->move(public_path('img'), $imageName);
            $imagePath = 'img/' . $imageName;
            $room->image = $imagePath;
        }
        $room->save();

        return redirect()->route('admin.habitaciones.rooms.index')
            ->with('message', 'La habitación ha sido actualizada!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id) {
        $room = Room::findOrFail($id);
        $this->authorize('delete', $room);
        $room->delete();
        return redirect()->route('admin.habitaciones.rooms.index')
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
