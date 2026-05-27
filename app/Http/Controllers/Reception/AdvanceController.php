<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdvanceController extends Controller
{
    public function index()
    {
        // Obtener reservas con anticipo pagado o confirmadas (que ya tienen abono)
        // y que aún no han sido marcadas como 'asignada'
        // Filtrar específicamente por abono > 0 e is_paid = true para asegurar que son de la web y pagadas
        $reservations = Order::whereIn('status', [Order::STATUS_RESERVA_PREVIA, Order::STATUS_ANTICIPO_PAGADO, 'confirmada'])
            ->where('down_payment_amount', '>', 0)
            ->where('is_paid', true)
            ->with(['roomType', 'user'])
            ->latest()
            ->get();

        return view('reception.anticipos', compact('reservations'));
    }
}
