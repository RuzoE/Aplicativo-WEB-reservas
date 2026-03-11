<?php

namespace App\Services\Reception;

use App\Models\Stay;
use App\Models\Folio;

class CheckOutService
{
    public function __construct(
        protected FolioService $folioService
    ) {}

    public function processCheckOut(Stay $stay): void
    {
        $folio = $stay->folios()->whereIn('status', ['Open', 'Closed'])->firstOrFail();

        // Validate balance only if it's open
        if ($folio->status === 'Open') {
            if (!$this->folioService->canClose($folio)) {
                throw new \Exception('Cannot checkout with pending balance: ' . $folio->balance);
            }

            // Close folio
            $this->folioService->closeFolio($folio);
        }

        // Update stay
        $stay->status = 'CheckedOut';
        $stay->actual_check_out_at = now();
        $stay->save();

        // Mark room as available
        if ($stay->room) {
            $stay->room->update(['status' => 'available']);
        }
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
