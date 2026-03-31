<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Stay;
use App\Services\Reception\CheckOutService;
use App\Events\Reception\StayEnded;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
            $invoice = $this->checkOutService->processCheckOut($stay);

            StayEnded::dispatch($stay);

            // Refrescar para cargar transacciones actualizadas
            $stay->load(['folios.charges', 'folios.payments', 'order', 'room', 'guest']);

            $pdf = Pdf::loadView('reception.invoice', compact('stay', 'invoice'));
            $filename = $this->buildComprobanteFilename($invoice->invoice_number);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Check-out completado exitosamente. Comprobante N° ' . $invoice->invoice_number . ' generado.',
                    'invoice_url' => route('reception.invoices.download', $invoice->id),
                ]);
            }

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error al procesar check-out.', [
                'stay_id' => $stayId,
                'user_id' => $request->user()?->id,
                'message' => $e->getMessage(),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No fue posible completar el check-out. Intenta nuevamente o contacta al administrador.'
                ], 422);
            }

            return back()->withErrors([
                'checkout' => 'No fue posible completar el check-out. Intenta nuevamente o contacta al administrador.'
            ]);
        }
    }

    public function download($invoiceId)
    {
        $invoice = \App\Models\Invoice::with('stay')->findOrFail($invoiceId);
        $stay = $invoice->stay;
        $stay->load(['folios.charges', 'folios.payments', 'order', 'room', 'guest']);

        $pdf = Pdf::loadView('reception.invoice', compact('stay', 'invoice'));
        $filename = $this->buildComprobanteFilename($invoice->invoice_number);

        return $pdf->download($filename);
    }

    private function buildComprobanteFilename(string $invoiceNumber): string
    {
        $downloadDate = Carbon::now()->format('Y-m-d_H-i');

        return 'Comprobante_de_pago_N_' . $invoiceNumber . '_' . $downloadDate . '.pdf';
    }
}
