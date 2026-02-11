<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Stay;
use App\Services\Reception\CheckOutService;
use App\Events\Reception\StayEnded;
use Illuminate\Http\Request;

class CheckOutController extends Controller
{
    public function __construct(
        protected CheckOutService $checkOutService
    ) {}

    public function store(Request $request, $stayId)
    {
        $stay = Stay::with('folios')->findOrFail($stayId);

        $this->authorize('checkOut', $stay);

        try {
            $this->checkOutService->processCheckOut($stay);

            StayEnded::dispatch($stay);

            return redirect()->route('reception.dashboard')->with('status', 'Check-out completado.');
        } catch (\Exception $e) {
            return back()->withErrors(['checkout' => $e->getMessage()]);
        }
    }
}
