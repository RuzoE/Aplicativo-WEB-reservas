<?php

namespace App\Policies;

use App\Models\Folio;
use App\Models\User;

class FolioPolicy
{
    public function view(User $user, Folio $folio): bool
    {
        return $user->hasAnyRole(['administrador', 'recepcion'])
            || $user->hasAnyPermission(['reception.folio.view', 'admin']);
    }

    public function postCharge(User $user, Folio $folio): bool
    {
        if ($folio->status !== 'Open') {
            return false;
        }

        return $user->hasAnyRole(['administrador', 'recepcion'])
            || $user->hasAnyPermission(['reception.folio.post_charge', 'admin']);
    }

    public function receivePayment(User $user, Folio $folio): bool
    {
        if ($folio->status !== 'Open') {
            return false;
        }

        return $user->hasAnyRole(['administrador', 'recepcion'])
            || $user->hasAnyPermission(['reception.folio.receive_payment', 'admin']);
    }

    public function close(User $user, Folio $folio): bool
    {
        return $user->hasAnyRole(['administrador', 'recepcion'])
            || $user->hasAnyPermission(['reception.checkout', 'admin']);
    }
}
