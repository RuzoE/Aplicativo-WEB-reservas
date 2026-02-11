<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:50'],
        ]);

        Role::firstOrCreate([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        return back()->with('success', 'Rol creado correctamente');
    }
}
