<?php

namespace App\Http\Controllers\Admin\Minibar;

use App\Http\Controllers\Controller;   // ← ¡esta línea hace falta!
use App\Models\MinibarProduct;
use App\Models\Compra;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts  = MinibarProduct::count();
        $totalCompras   = Compra::count();
        $latestCompras = Compra::orderBy('created_at','asc')->get();

        return view('admin.minibar.dashboard', compact('totalProducts','totalCompras','latestCompras'));
    }
}
