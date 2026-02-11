<?php
namespace App\Http\Controllers\Minibar\User;

use App\Http\Controllers\Controller;
use App\Models\MinibarProduct;
use App\Models\BebidaType;

class LandingController extends Controller
{
    public function index()
    {
        // 1) Categorías con contador
        $categories = BebidaType::withCount('products')->get();

        // 2) Destacados (ej: 8 primeros productos)
        $featured = MinibarProduct::with('type')
                      ->limit(8)
                      ->get();

        return view('minibar.landing', compact('categories','featured'));
    }
}
