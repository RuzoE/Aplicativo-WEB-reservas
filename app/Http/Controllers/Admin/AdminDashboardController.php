<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Room;
use App\Models\Bebida;
use App\Models\Compra;
use App\Models\Stay;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // Datos de Reservas
        $totalRooms = Room::sum('total_room');
        $reservedRoom = Order::where('status', 'confirmada')
            ->whereDate('check_in', '>', Carbon::now())
            ->count();
        
        $expectedArrivalsToday = Order::where('status', 'confirmada')
            ->whereDoesntHave('stays')
            ->whereDate('check_in', Carbon::today())
            ->count();

        // Datos de Minibar
        $totalProductos = Bebida::count();
        $totalCompras = Compra::count();

        // Datos de Recepción (Check-ins ya realizados hoy)
        $checkInsRealizadosHoy = Stay::whereDate('arrival_at', Carbon::today())->count();
        $huespedesEnCasa = Stay::where('status', 'InHouse')->count();

        return view('admin.index', compact(
            'totalRooms',
            'reservedRoom',
            'expectedArrivalsToday',
            'totalProductos',
            'totalCompras',
            'checkInsRealizadosHoy',
            'huespedesEnCasa'
        ));
    }
}
