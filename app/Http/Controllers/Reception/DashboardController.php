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
        $arrivals = Stay::whereDate('arrival_at', now()->toDateString())->get();
        $departures = Stay::whereDate('departure_at', now()->toDateString())->get();
        $inHouse = Stay::where('status', 'InHouse')->get();

        // Reservas pendientes de check-in (orders sin stay asociado y fecha de check-in <= hoy)
        $pendingCheckIns = Order::whereDoesntHave('stays')
            ->whereDate('check_in', '<=', now())
            ->where('check_out', '>', now())
            ->with(['room.roomtype', 'user'])
            ->orderBy('check_in', 'asc')
            ->get();

        return view('reception.dashboard', compact('arrivals', 'departures', 'inHouse', 'pendingCheckIns'));
    }
}
