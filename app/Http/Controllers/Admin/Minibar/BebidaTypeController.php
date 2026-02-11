<?php
namespace App\Http\Controllers\Admin\Minibar;

use App\Http\Controllers\Controller;
use App\Models\BebidaType;
use Illuminate\Http\Request;

class BebidaTypeController extends Controller
{
    public function index()
    {
        // Renombramos la variable a $types
        $types = BebidaType::latest()->paginate(10);

        // Y compactamos 'types'
        return view('admin.minibar.bebida-types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.minibar.bebida-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:bebida_types,nombre',
        ]);

        BebidaType::create($request->only('nombre'));

        return redirect()
            ->route('admin.minibar.bebida-types.index')
            ->with('success','Tipo de bebida creado.');
    }

    public function edit(BebidaType $bebida_type)
    {
        return view('admin.minibar.bebida-types.edit', compact('bebida_type'));
    }

    public function update(Request $request, BebidaType $bebida_type)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:bebida_types,nombre,'.$bebida_type->id,
        ]);

        $bebida_type->update($request->only('nombre'));

        return redirect()
            ->route('admin.minibar.bebida-types.index')
            ->with('success','Tipo de bebida actualizado.');
    }

    public function destroy(BebidaType $bebida_type)
    {
        $bebida_type->delete();

        return back()->with('success','Tipo de bebida eliminado.');
    }
}
