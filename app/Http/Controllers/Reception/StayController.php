<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Http\Request;

class StayController extends Controller
{
    /**
     * Busca usuarios registrados para vincular a una reserva (walk-in).
     */
    public function searchUsers(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([]);
        }

        $users = User::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'last_name', 'email']);

        return response()->json($users);
    }

    /**
     * Vincula manualmente un usuario a una estancia activa.
     */
    public function linkUser(Request $request, Stay $stay)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if ($stay->status !== 'InHouse') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden vincular usuarios a reservas activas (InHouse).',
            ], 422);
        }

        $stay->user_id = $request->user_id;
        $stay->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuario vinculado correctamente a la reserva.',
            'user' => $stay->user->only(['id', 'name', 'last_name', 'email']),
        ]);
    }
}
