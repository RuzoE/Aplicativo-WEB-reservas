<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function index()
    {
        // Asegura que existan los roles operativos para mostrarlos en el formulario.
        foreach (['reservas', 'minibar', 'recepcion', 'mantenimiento'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Filtrar solo usuarios con roles: administrador, reservas, minibar, recepcion, mantenimiento
        $empleados = User::with('roles')
            ->whereHas('roles', function ($query) {
            $query->whereIn('name', ['administrador', 'reservas', 'minibar', 'recepcion', 'mantenimiento']);
        })
            ->orderBy('id', 'asc')
            ->get();

        // Incluir recepcion y mantenimiento en las opciones disponibles (sin exponer administrador)
        $roles = Role::whereIn('name', ['reservas', 'minibar', 'recepcion', 'mantenimiento'])->pluck('name'); // nombres para asignación rápida
        $rolesCreate = Role::whereIn('name', ['reservas', 'minibar', 'recepcion', 'mantenimiento'])->pluck('name', 'id'); // id => nombre para formulario crear
        return view('admin.empleados.index', compact('empleados', 'roles', 'rolesCreate'));
    }

    public function create()
    {
        // El formulario de creación está integrado en la vista index,
        // esto evita el error 500 si se visita la ruta manualmente
        return redirect()->route('admin.empleados.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        // Si en User tienes cast 'password' => 'hashed', no necesitas Hash::make.
        $user = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
        ]);

        $roleName = Role::find($data['role_id'])->name;
        $user->assignRole($roleName);

        return redirect()->route('admin.empleados.index')
            ->with('success', 'El empleado "' . $user->name . '" ha sido creado correctamente.');
    }

    public function update(Request $request, User $empleado)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,' . $empleado->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'role_id' => ['nullable', 'exists:roles,id'],
        ]);

        $empleado->fill(collect($data)->except('password', 'role_id')->toArray());
        if (!empty($data['password'])) {
            $empleado->password = $data['password'];
        }
        $empleado->save();

        if (!empty($data['role_id'])) {
            $roleName = Role::find($data['role_id'])->name;
            $empleado->syncRoles([$roleName]); // reemplaza rol actual por el nuevo
        }

        return redirect()
            ->route('admin.empleados.index')
            ->with('success', 'Los datos de "' . $empleado->name . '" han sido actualizados.');
    }

    public function destroy(User $empleado)
    {
        $empleado->delete();

        return redirect()
            ->route('admin.empleados.index')
            ->with('success', 'El empleado ha sido eliminado del sistema.');
    }

    // POST: /admin/empleados/{user}/roles
    public function assignRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user->syncRoles([$data['role']]); // o ->assignRole($data['role']) si no quieres reemplazar
        return back()->with('success', 'Rol asignado.');
    }
}
