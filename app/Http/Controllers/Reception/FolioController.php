<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Stay;
use App\Services\Reception\FolioService;
use App\Events\Reception\ChargePosted;
use App\Events\Reception\PaymentReceived;
use Illuminate\Http\Request;

class FolioController extends Controller
{
    public function __construct(
        protected FolioService $folioService
    ) {}

    public function show($stayId)
    {
        $stay = Stay::with(['folios.charges', 'folios.payments'])->findOrFail($stayId);
        $folio = $stay->folios()->where('status', 'Open')->first();

        if ($folio) {
            $this->authorize('view', $folio);
        }

        return view('reception.folio', compact('stay', 'folio'));
    }

    public function postCharge(Request $request, $stayId)
    {
        $stay = Stay::with('folios')->findOrFail($stayId);
        $folio = $stay->folios()->where('status', 'Open')->firstOrFail();

        $this->authorize('postCharge', $folio);

        $data = $request->validate([
            'source' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
        ]);

        $charge = $this->folioService->postCharge($folio, $data, $request->user());

        ChargePosted::dispatch($charge);

        return back()->with('status', 'Cargo agregado al folio.');
    }

    public function postPayment(Request $request, $stayId)
    {
        $stay = Stay::with('folios')->findOrFail($stayId);
        $folio = $stay->folios()->where('status', 'Open')->firstOrFail();

        $this->authorize('receivePayment', $folio);

        $data = $request->validate([
            'method' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'external_ref' => 'nullable|string',
        ]);

        $payment = $this->folioService->receivePayment($folio, $data, $request->user());

        PaymentReceived::dispatch($payment);

        return back()->with('status', 'Pago registrado.');
    }
}
