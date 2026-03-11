<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Stay;
use App\Services\Reception\CheckOutService;
use App\Events\Reception\StayEnded;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

            // Refrescar para cargar transacciones actualizadas (por si las hay en el processCheckOut)
            $stay->load(['folios.charges', 'folios.payments']);

            $pdf = Pdf::loadView('reception.invoice', compact('stay'));

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Check-out completado exitosamente.',
                    // No podemos retornar el PDF directamente en JSON, requeriría una URL de descarga
                    // Pero la vista actual no usa Ajax para el submit final.
                ]);
            }

            // Descargar el PDF generado
            return $pdf->download('factura-estancia-' . $stay->id . '.pdf');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return back()->withErrors(['checkout' => $e->getMessage()]);
        }
    }
}
