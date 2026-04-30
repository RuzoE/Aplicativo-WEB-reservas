<?php

namespace App\Http\Controllers\Admin\Habitaciones;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $types = RoomType::all();
        return view('admin.roomtypes.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roomtypes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => ['required', 'unique:room_types,name']
        ]);

        $type = RoomType::create($validatedData);

        registrarAuditoria(
            'CREATE',
            'habitaciones',
            $type->id,
            'Tipo de habitación creado: ' . $validatedData['name'],
            auth()->id()
        );

        return redirect()->route('admin.habitaciones.tipos-habitacion.index')
            ->with('success', '¡El tipo de habitación "' . $validatedData['name'] . '" ha sido creado exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RoomType $roomType)
    {
    //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $type = RoomType::findOrFail($id);
        // $this->authorize('update', $type);

        return view('admin.roomtypes.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'unique:room_types,name,' . $id]
        ]);

        $type = RoomType::findOrFail($id);
        $type->update($validatedData);

        registrarAuditoria(
            'UPDATE',
            'habitaciones',
            $type->id,
            'Tipo de habitación actualizado: ' . $validatedData['name'],
            auth()->id()
        );

        return redirect()->route('admin.habitaciones.tipos-habitacion.index')
            ->with('success', '¡El tipo de habitación "' . $validatedData['name'] . '" ha sido actualizado correctamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        abort_unless(auth()->user()->hasRole('administrador'), 403, 'No autorizado para eliminar.');

        $type = RoomType::findOrFail($id);
        $typeName = $type->name;
        $type->delete();

        registrarAuditoria(
            'DELETE',
            'habitaciones',
            $id,
            'Tipo de habitación eliminado: ' . $typeName,
            auth()->id()
        );

        return redirect()->route('admin.habitaciones.tipos-habitacion.index')
            ->with('success', '¡El tipo de habitación ha sido eliminado del sistema!');
    }
}
