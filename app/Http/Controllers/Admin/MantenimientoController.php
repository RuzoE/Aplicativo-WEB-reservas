<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class MantenimientoController extends Controller
{
    public function index()
    {
        return view('admin.mantenimiento');
    }

    public function create()
    {
        return view('admin.mantenimiento.create');
    }

    public function history()
    {
        return view('admin.mantenimiento.history');
    }

    public function settings()
    {
        return view('admin.mantenimiento.settings');
    }
}
