<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Stay;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $arrivals = Stay::whereDate('arrival_at', now()->toDateString())->get();
        $departures = Stay::whereDate('departure_at', now()->toDateString())->get();
        $inHouse = Stay::where('status', 'InHouse')->get();

        return view('reception.dashboard', compact('arrivals', 'departures', 'inHouse'));
    }
}
