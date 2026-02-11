<?php

namespace App\Http\Controllers\Minibar\User;

use App\Http\Controllers\Controller;
use App\Models\Bebida;

class BebidaController extends Controller
{
    public function show(Bebida $bebida)
    {
        return view('minibar.bebida.show', compact('bebida'));
    }
    // …otros métodos resource…
}
