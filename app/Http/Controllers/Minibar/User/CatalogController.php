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

        // Mostrar solo 6 categorías en total: 3 alcohólicas + 3 no alcohólicas
        $alcoholicCategories = BebidaType::withCount('products')
            ->where('es_alcoholica', true)
            ->orderByDesc('products_count')
            ->orderBy('nombre')
            ->take(3)
            ->get();

        $nonAlcoholicCategories = BebidaType::withCount('products')
            ->where('es_alcoholica', false)
            ->orderByDesc('products_count')
            ->orderBy('nombre')
            ->take(3)
            ->get();

        $landingCategories = $nonAlcoholicCategories
            ->concat($alcoholicCategories)
            ->values();

        return view('minibar.landing', compact('featured', 'landingCategories'));
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

        $alcoholicProducts = $products->getCollection()->filter(function ($product) {
            return $this->isAlcoholic($product);
        })->values();

        $nonAlcoholicProducts = $products->getCollection()->filter(function ($product) {
            return !$this->isAlcoholic($product);
        })->values();

        // Para el selector de tipos
        $categories = BebidaType::withCount('products')->get();
        $selectedCategory = $tipo ? $categories->firstWhere('id', (int) $tipo) : null;
        $selectedIsAlcoholic = $selectedCategory ? (bool) $selectedCategory->es_alcoholica : null;

        // Si hay tipo seleccionado, muestra solo su bloque (alcoholico/no alcoholico).
        $showNonAlcoholicSection = $selectedIsAlcoholic !== true;
        $showAlcoholicSection = $selectedIsAlcoholic !== false;

        return view(
            'minibar.catalogo',
            compact(
                'products',
                'categories',
                'q',
                'tipo',
                'alcoholicProducts',
                'nonAlcoholicProducts',
                'showNonAlcoholicSection',
                'showAlcoholicSection'
            )
        );
    }

    private function isAlcoholic(MinibarProduct $product): bool
    {
        if ($product->type && isset($product->type->es_alcoholica)) {
            return (bool) $product->type->es_alcoholica;
        }

        $typeName = mb_strtolower((string) optional($product->type)->nombre);
        $name = mb_strtolower((string) $product->nombre);

        $alcoholKeywords = [
            'alcohol',
            'licor',
            'cerveza',
            'vino',
            'ron',
            'whisky',
            'tequila',
            'vodka',
            'coctel',
            'cocktail',
            'sidra',
            'gin',
            'brandy',
            'champagne',
            'mezcal',
            'pisco',
            'aguardiente',
        ];

        foreach ($alcoholKeywords as $keyword) {
            if (str_contains($typeName, $keyword) || str_contains($name, $keyword)) {
                return true;
            }
        }

        return false;
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
