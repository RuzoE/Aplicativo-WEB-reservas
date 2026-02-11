<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MinibarProduct;
use App\Models\BebidaType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MinibarProductController extends Controller
{
    public function index()
    {
        return response()->json(MinibarProduct::with('type')->paginate(10));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'bebida_type_id' => 'required|exists:bebida_types,id',
            'cantidad' => 'nullable|integer|min:0',
            'estado' => 'nullable|boolean',
        ]);

        $data['stock'] = $data['cantidad'] ?? 0;

        $item = MinibarProduct::create($data);

        return response()->json([
            'message' => 'Producto creado correctamente',
            'data' => $item->load('type')
        ], 201);
    }

    public function show(MinibarProduct $minibar_product)
    {
        return response()->json($minibar_product->load('type'));
    }

    public function update(Request $request, MinibarProduct $minibar_product)
    {
        $data = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'precio' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'cantidad' => 'sometimes|integer|min:0',
            'descripcion' => 'sometimes|nullable|string',
            'estado' => 'sometimes|boolean',
        ]);

        if (isset($data['cantidad'])) {
            $data['stock'] = $data['cantidad'];
        }

        $minibar_product->update($data);

        return response()->json([
            'message' => 'Producto actualizado',
            'data' => $minibar_product->fresh()->load('type')
        ]);
    }

    public function destroy(MinibarProduct $minibar_product)
    {
        $minibar_product->delete();

        return response()->json([
            'message' => 'Producto eliminado'
        ], 204);
    }
}

