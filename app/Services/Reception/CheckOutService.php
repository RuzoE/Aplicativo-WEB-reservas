<?php

namespace App\Services\Reception;

use App\Models\Stay;
use App\Models\Folio;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class CheckOutService
{
    public function __construct(
        protected FolioService $folioService
    ) {}

    public function processCheckOut(Stay $stay): Invoice
    {
        $folio = $stay->folios()->whereIn('status', ['Open', 'Closed'])->firstOrFail();

        // 1. Billing Calculations (this includes the reservation total)
        $billing = $stay->getBillingBreakdown();

        // 2. Re-calculate folio balance to be sure
        $this->folioService->recalculateBalance($folio);

        // 3. (Optional) Validate balance removed as per user request to allow flexible checkout

        // 4. Close folio if it's still open
        if ($folio->status === 'Open') {
            $folio->update(['status' => 'Closed']);
        }

        // 4. Create comprobante with plain sequential number (n+1)
        $invoice = DB::transaction(function () use ($stay, $folio, $billing) {
            $lastInvoice = Invoice::query()->orderByDesc('id')->lockForUpdate()->first();
            $lastNumber = (int) preg_replace('/\D+/', '', (string) ($lastInvoice?->invoice_number ?? '0'));
            $nextNumber = (string) ($lastNumber + 1);

            return Invoice::create([
                'stay_id' => $stay->id,
                'folio_id' => $folio->id,
                'invoice_number' => $nextNumber,
                'subtotal' => $billing->subtotal,
                'tax' => $billing->iva,
                'total' => $billing->total_final,
                'is_paid' => ($billing->balance <= 0.01),
                'payment_method' => $folio->payments()->latest()->first()?->method ?? 'Mixed',
            ]);
        });

        // 5. Update stay
        $stay->status = 'CheckedOut';
        $stay->actual_check_out_at = now();
        $stay->save();

        // 6. Mark room as available
        if ($stay->room) {
            $stay->room->update(['status' => 'available']);

            registrarAuditoria(
                'UPDATE',
                'habitaciones',
                $stay->room->id,
                'Habitacion ID ' . $stay->room->id . ' cambiada a estado available por check-out de stay ID ' . $stay->id,
                auth()->id()
            );
        }

        registrarAuditoria(
            'CHECK_OUT',
            'recepcion',
            $stay->id,
            'Check-out completado para stay ID ' . $stay->id . ' con comprobante ' . $invoice->invoice_number,
            auth()->id()
        );

        return $invoice;
    }

    public function canCheckOut(Stay $stay): bool
    {
        $folio = $stay->folios()->where('status', 'Open')->first();

        if (!$folio) {
            return false;
        }

        return $this->folioService->canClose($folio);
    }
}
