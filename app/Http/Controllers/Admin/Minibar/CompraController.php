<?php
namespace App\Http\Controllers\Admin\Minibar;

use App\Http\Controllers\Controller;
use App\Models\Compra;

class CompraController extends Controller
{
    public function index()
    {
        // Trae las compras con su usuario y productos
        $compras = Compra::with(['user','productos'])
                         ->orderBy('id', 'asc')
                         ->paginate(10);

        return view('admin.minibar.ventas.index', compact('compras'));
    }

   /**
     * Muestra el detalle de una compra específica.
     */
    public function show($id)
    {
        $compra = Compra::with(['user', 'productos'])->findOrFail($id);

        return view('admin.minibar.ventas.show', compact('compra'));
    }
}
