<?php

namespace App\Http\Controllers\Minibar\User;

use App\Http\Controllers\Controller;
use App\Models\MinibarProduct;
use App\Models\BebidaType;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * 1) Landing público: slider + bebidas destacadas
     * URL: GET /minibar
     * Ruta nombrada: minibar.landing
     */
    public function landing(Request $request)
    {
        // Mostrar siempre la landing, incluso si el usuario es administrador

        // 8 bebidas más recientes
        $featured = MinibarProduct::with('type')
            ->latest()
            ->take(8)
            ->get();

        // las categorías para mostrarlas en el partial si quieres
        $categories = BebidaType::withCount('products')->get();

        return view('minibar.landing', compact('featured', 'categories'));
    }

    /**
     * 2) Catálogo completo
     * URL: GET /minibar/catalogo
     * Ruta nombrada: minibar.catalogo
     */
    public function index(Request $request)
    {
        // Mostrar siempre el catálogo, incluso si el usuario es administrador

        // Filtros
        $q    = $request->input('q');
        $tipo = $request->input('tipo');

        // Query base
        $query = MinibarProduct::with('type');

        if ($q) {
            $query->where('nombre', 'like', "%{$q}%");
        }

        if ($tipo) {
            $query->where('bebida_type_id', $tipo);
        }

        // Paginación 12 por página, conserva filtros en la URL
        $products = $query
            ->orderBy('nombre')
            ->paginate(12)
            ->appends(['q' => $q, 'tipo' => $tipo]);

        // Para el selector de tipos
        $categories = BebidaType::withCount('products')->get();

        return view('minibar.catalogo', compact('products', 'categories', 'q', 'tipo'));
    }

    /**
     * 3) Detalle de un producto
     * URL: GET /minibar/{bebida}
     * Ruta nombrada: minibar.bebida.show
     */
    public function show(MinibarProduct $bebida)
    {
        // Puedes cargar relaciones si quieres más datos
        $bebida->load('type', 'reviews');

        return view('minibar.bebida.show', compact('bebida'));
    }
}
