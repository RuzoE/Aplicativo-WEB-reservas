<?php

namespace App\Http\Controllers\Admin\Minibar;

use App\Http\Controllers\Controller;
use App\Models\MinibarProduct;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /**
     * Display a listing of low stock notifications.
     */
    public function index()
    {
        // Fetch all minibar products with stock <= 5
        $lowStockProducts = MinibarProduct::with('type')
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->get();

        return view('admin.minibar.notificaciones.index', compact('lowStockProducts'));
    }
}
