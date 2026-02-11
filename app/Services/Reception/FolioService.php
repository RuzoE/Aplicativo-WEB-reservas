<?php

namespace App\Services\Reception;

use App\Models\Folio;
use App\Models\Charge;
use App\Models\Payment;
use App\Models\User;

class FolioService
{
    public function postCharge(Folio $folio, array $chargeData, User $postedBy): Charge
    {
        $charge = new Charge($chargeData + [
            'posted_by' => $postedBy->id,
            'posted_at' => now(),
        ]);

        $folio->charges()->save($charge);

        // Update balance
        $this->recalculateBalance($folio);

        return $charge;
    }

    public function receivePayment(Folio $folio, array $paymentData, User $receivedBy): Payment
    {
        $payment = new Payment($paymentData + [
            'received_by' => $receivedBy->id,
            'received_at' => now(),
        ]);

        $folio->payments()->save($payment);

        // Update balance
        $this->recalculateBalance($folio);

        return $payment;
    }

    public function recalculateBalance(Folio $folio): void
    {
        $totalCharges = $folio->charges()
            ->selectRaw('COALESCE(SUM(amount + COALESCE(tax, 0)), 0) as total')
            ->value('total');

        $totalPayments = $folio->payments()
            ->selectRaw('COALESCE(SUM(amount), 0) as total')
            ->value('total');

        $folio->balance = $totalCharges - $totalPayments;
        $folio->save();
    }

    public function canClose(Folio $folio): bool
    {
        return abs($folio->balance) < 0.01; // Allow for floating point precision
    }

    public function closeFolio(Folio $folio): void
    {
        if (!$this->canClose($folio)) {
            throw new \Exception('Cannot close folio with non-zero balance');
        }

        $folio->status = 'Closed';
        $folio->save();
    }
}
