<?php

namespace App\Http\Controllers\Admin\Minibar;

use App\Http\Controllers\Controller;
use App\Models\MinibarProduct;
use App\Models\BebidaType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MinibarProductController extends Controller
{
    /**
     * LISTA (vista Blade)
     */
    public function index()
    {
        $bebidas = MinibarProduct::with('type')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Siempre vista (no JSON)
        return view('admin.minibar.bebidas.index', compact('bebidas'));
    }

    /**
     * FORM CREAR (vista Blade)
     */
    public function create()
    {
        $bebidaTypes = BebidaType::orderBy('nombre')->get();
        return view('admin.minibar.bebidas.create', compact('bebidaTypes'));
    }

    /**
     * GUARDAR (redirect + mensaje)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'bebida_type_id' => ['required', 'exists:bebida_types,id'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'descripcion' => ['nullable', 'string'],
            'estado' => ['nullable', 'boolean'],
            'imagen' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('minibar', 's3');
        }

        $bebida = MinibarProduct::create($data);

        registrarAuditoria(
            'CREATE',
            'minibar',
            $bebida->id,
            'Bebida creada: ' . $data['nombre'] . ', precio ' . $data['precio'],
            auth()->id()
        );

        return redirect()
            ->route('admin.minibar.bebidas.index')
            ->with('success', 'La bebida "' . $data['nombre'] . '" ha sido creada correctamente.');
    }

    /**
     * MOSTRAR (opcional, vista Blade si la tienes)
     */
    public function show(MinibarProduct $bebida)
    {
        return view('admin.minibar.bebidas.show', compact('bebida'));
    }

    /**
     * FORM EDITAR (vista Blade)
     */
    public function edit(MinibarProduct $bebida)
    {
        $bebidaTypes = BebidaType::orderBy('nombre')->get();
        return view('admin.minibar.bebidas.edit', compact('bebida', 'bebidaTypes'));
    }

    /**
     * ACTUALIZAR (redirect + mensaje)
     */
    public function update(Request $request, MinibarProduct $bebida)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'bebida_type_id' => ['required', 'exists:bebida_types,id'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'descripcion' => ['nullable', 'string'],
            'estado' => ['nullable', 'boolean'],
            'imagen' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('imagen')) {
            // borra la anterior si existe
            if ($bebida->imagen) {
                Storage::disk('s3')->delete($bebida->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('minibar', 's3');
        }

        $bebida->update($data);

        registrarAuditoria(
            'UPDATE',
            'minibar',
            $bebida->id,
            'Bebida actualizada: ' . $data['nombre'],
            auth()->id()
        );

        return redirect()
            ->route('admin.minibar.bebidas.index')
            ->with('success', 'La bebida "' . $data['nombre'] . '" ha sido actualizada.');
    }

    /**
     * ELIMINAR (redirect + mensaje)
     */
    public function destroy(MinibarProduct $bebida)
    {
        abort_unless(auth()->user()->hasRole('administrador'), 403, 'No autorizado para eliminar.');

        $bebidaNombre = $bebida->nombre;
        $bebidaId = $bebida->id;

        if ($bebida->imagen) {
            Storage::disk('s3')->delete($bebida->imagen);
        }

        $bebida->delete();

        registrarAuditoria(
            'DELETE',
            'minibar',
            $bebidaId,
            'Bebida eliminada: ' . $bebidaNombre . ' (ID ' . $bebidaId . ')',
            auth()->id()
        );

        return redirect()
            ->route('admin.minibar.bebidas.index')
            ->with('success', 'La bebida ha sido eliminada del inventario.');
    }
}
