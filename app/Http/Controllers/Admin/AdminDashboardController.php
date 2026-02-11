<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Room;
use App\Models\Bebida;
use App\Models\Compra;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // Datos de Reservas
        $totalRooms = Room::sum('total_room');
        $reservedRoom = Order::whereDate('check_in', '>=', Carbon::now())->count();

        // Datos de Minibar
        $totalProductos = Bebida::count();
        $totalCompras = Compra::count();

        return view('admin.index', compact(
            'totalRooms',
            'reservedRoom',
            'totalProductos',
            'totalCompras'
        ));
    }
}
