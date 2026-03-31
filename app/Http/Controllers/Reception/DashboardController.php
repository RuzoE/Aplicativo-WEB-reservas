<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Stay;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Personas que ya hicieron check-in hoy
        $checkedInToday = Stay::whereDate('arrival_at', now()->toDateString())->get();
        
        // Reservas confirmadas que se esperan hoy pero no han llegado
        $expectedArrivalsToday = Order::where('status', 'confirmada')
            ->whereDoesntHave('stays')
            ->whereDate('check_in', now()->toDateString())
            ->get();

        $departures = Stay::whereDate('departure_at', now()->toDateString())->get();
        $inHouse = Stay::where('status', 'InHouse')->get();

        // Lista completa de reservas pendientes (confirmadas, para hoy o pasadas que no han vencido)
        $pendingCheckIns = Order::where('status', 'confirmada')
            ->whereDoesntHave('stays')
            ->whereDate('check_in', '<=', now())
            ->where('check_out', '>', now())
            ->with(['room.roomtype', 'user'])
            ->orderBy('check_in', 'asc')
            ->get();

        return view('reception.dashboard', compact(
            'checkedInToday', 
            'expectedArrivalsToday', 
            'departures', 
            'inHouse', 
            'pendingCheckIns'
        ));
    }
}
