<?php
namespace App\Http\Controllers\Admin\Minibar;

use App\Http\Controllers\Controller;
use App\Models\BebidaType;
use Illuminate\Http\Request;

class BebidaTypeController extends Controller
{
    public function index()
    {
        $types = BebidaType::where('es_alcoholica', true)->latest()->paginate(10);
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

        $type = BebidaType::create([
            'nombre' => $request->nombre,
            'es_alcoholica' => true,
        ]);

        registrarAuditoria(
            'CREATE',
            'minibar',
            $type->id,
            'Tipo de bebida creado: ' . $request->nombre,
            auth()->id()
        );

        return redirect()
            ->route('admin.minibar.bebida-types.index')
            ->with('success', 'El tipo de bebida "' . $request->nombre . '" ha sido creado exitosamente.');
    }

    public function edit(BebidaType $bebida_type)
    {
        abort_unless($bebida_type->es_alcoholica, 404);
        return view('admin.minibar.bebida-types.edit', compact('bebida_type'));
    }

    public function update(Request $request, BebidaType $bebida_type)
    {
        abort_unless($bebida_type->es_alcoholica, 404);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:bebida_types,nombre,' . $bebida_type->id,
        ]);

        $bebida_type->update($request->only('nombre'));

        registrarAuditoria(
            'UPDATE',
            'minibar',
            $bebida_type->id,
            'Tipo de bebida actualizado: ' . $request->nombre,
            auth()->id()
        );

        return redirect()
            ->route('admin.minibar.bebida-types.index')
            ->with('success', 'El tipo de bebida "' . $request->nombre . '" ha sido actualizado.');
    }

    public function destroy(BebidaType $bebida_type)
    {
        abort_unless(auth()->user()->hasRole('administrador'), 403, 'No autorizado para eliminar.');
        abort_unless($bebida_type->es_alcoholica, 404);

        $typeName = $bebida_type->nombre;
        $typeId = $bebida_type->id;
        $bebida_type->delete();

        registrarAuditoria(
            'DELETE',
            'minibar',
            $typeId,
            'Tipo de bebida eliminado: ' . $typeName . ' (ID ' . $typeId . ')',
            auth()->id()
        );

        return back()->with('success', 'El tipo de bebida ha sido eliminado correctamente.');
    }

    public function indexNonAlcoholic()
    {
        $types = BebidaType::where('es_alcoholica', false)->latest()->paginate(10);
        return view('admin.minibar.bebida-types-na.index', compact('types'));
    }

    public function createNonAlcoholic()
    {
        return view('admin.minibar.bebida-types-na.create');
    }

    public function storeNonAlcoholic(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:bebida_types,nombre',
        ]);

        $type = BebidaType::create([
            'nombre' => $request->nombre,
            'es_alcoholica' => false,
        ]);

        registrarAuditoria(
            'CREATE',
            'minibar',
            $type->id,
            'Tipo de bebida no alcohólica creado: ' . $request->nombre,
            auth()->id()
        );

        return redirect()
            ->route('admin.minibar.bebida-types-na.index')
            ->with('success', 'El tipo de bebida no alcohólica "' . $request->nombre . '" ha sido creado exitosamente.');
    }

    public function editNonAlcoholic(BebidaType $bebida_type)
    {
        abort_if($bebida_type->es_alcoholica, 404);
        return view('admin.minibar.bebida-types-na.edit', compact('bebida_type'));
    }

    public function updateNonAlcoholic(Request $request, BebidaType $bebida_type)
    {
        abort_if($bebida_type->es_alcoholica, 404);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:bebida_types,nombre,' . $bebida_type->id,
        ]);

        $bebida_type->update($request->only('nombre'));

        registrarAuditoria(
            'UPDATE',
            'minibar',
            $bebida_type->id,
            'Tipo de bebida no alcohólica actualizado: ' . $request->nombre,
            auth()->id()
        );

        return redirect()
            ->route('admin.minibar.bebida-types-na.index')
            ->with('success', 'El tipo de bebida no alcohólica "' . $request->nombre . '" ha sido actualizado.');
    }

    public function destroyNonAlcoholic(BebidaType $bebida_type)
    {
        abort_unless(auth()->user()->hasRole('administrador'), 403, 'No autorizado para eliminar.');
        abort_if($bebida_type->es_alcoholica, 404);

        $typeName = $bebida_type->nombre;
        $typeId = $bebida_type->id;
        $bebida_type->delete();

        registrarAuditoria(
            'DELETE',
            'minibar',
            $typeId,
            'Tipo de bebida no alcohólica eliminado: ' . $typeName . ' (ID ' . $typeId . ')',
            auth()->id()
        );

        return back()->with('success', 'El tipo de bebida no alcohólica ha sido eliminado correctamente.');
    }
}
