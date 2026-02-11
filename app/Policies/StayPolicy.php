<?php

namespace App\Policies;

use App\Models\Stay;
use App\Models\User;

class StayPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrador', 'recepcion', 'reservas'])
            || $user->hasAnyPermission(['reception.access', 'admin']);
    }

    public function view(User $user, Stay $stay): bool
    {
        return $user->hasAnyRole(['administrador', 'recepcion', 'reservas'])
            || $user->hasAnyPermission(['reception.access', 'admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrador', 'recepcion'])
            || $user->hasAnyPermission(['reception.checkin', 'admin']);
    }

    public function update(User $user, Stay $stay): bool
    {
        return $user->hasAnyRole(['administrador', 'recepcion'])
            || $user->hasAnyPermission(['reception.checkin', 'admin']);
    }

    public function delete(User $user, Stay $stay): bool
    {
        return $user->hasRole('administrador')
            || $user->hasPermissionTo('admin');
    }

    public function checkOut(User $user, Stay $stay): bool
    {
        return $user->hasAnyRole(['administrador', 'recepcion'])
            || $user->hasAnyPermission(['reception.checkout', 'admin']);
    }

    public function moveRoom(User $user, Stay $stay): bool
    {
        return $user->hasAnyRole(['administrador', 'recepcion'])
            || $user->hasAnyPermission(['reception.room_move', 'admin']);
    }
}
