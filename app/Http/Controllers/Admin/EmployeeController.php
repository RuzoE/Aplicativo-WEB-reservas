<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\AllowedEmailDomain;
use App\Rules\AlphaSpace;
use App\Rules\PhoneNumberByPrefix;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
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
            'name' => ['required', new AlphaSpace, 'max:100'],
            'last_name' => ['required', new AlphaSpace, 'max:100'],
            'email' => ['required', 'email', 'max:150', new AllowedEmailDomain(), 'unique:users,email'],
            'phone' => ['required', new PhoneNumberByPrefix()],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $role = Role::find($data['role_id']);
        
        // Si en User tienes cast 'password' => 'hashed', no necesitas Hash::make.
        $user = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'is_employee' => true,
            'employee_department' => $role->name,
        ]);

        $user->assignRole($role->name);

        registrarAuditoria(
            'CREATE',
            'usuarios',
            $user->id,
            'Empleado creado: ' . $user->name . ' ' . ($user->last_name ?? '') . ' con rol ' . $role->name,
            auth()->id() ?? $user->id,
            ['skip_duplicate' => false]
        );

        registrarAuditoria(
            'ROLE_CHANGE',
            'usuarios',
            $user->id,
            'Rol inicial asignado al usuario ID ' . $user->id . ': ' . $role->name,
            auth()->id() ?? $user->id,
            ['skip_duplicate' => false]
        );

        return redirect()->route('admin.empleados.index')
            ->with('success', 'El empleado "' . $user->name . '" ha sido creado correctamente.');
    }

    public function update(Request $request, User $empleado)
    {
        $data = $request->validate([
            'name' => ['required', new AlphaSpace, 'max:100'],
            'last_name' => ['nullable', new AlphaSpace, 'max:100'],
            'email' => ['required', 'email', 'max:150', new AllowedEmailDomain(), 'unique:users,email,' . $empleado->id],
            'phone' => ['nullable', new PhoneNumberByPrefix()],
            'password' => ['nullable', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
            'role_id' => ['nullable', 'exists:roles,id'],
        ]);

        $empleado->fill(collect($data)->except('password', 'role_id')->toArray());
        
        if (!empty($data['password'])) {
            $empleado->password = $data['password'];

            registrarAuditoria(
                'PASSWORD_CHANGE',
                'usuarios',
                $empleado->id,
                'Contraseña actualizada para usuario ID ' . $empleado->id,
                auth()->id() ?? $empleado->id,
                ['skip_duplicate' => false]
            );
        }
        
        $empleado->save();

        registrarAuditoria(
            'UPDATE',
            'usuarios',
            $empleado->id,
            'Empleado actualizado: ' . $empleado->name . ' ' . ($empleado->last_name ?? ''),
            auth()->id() ?? $empleado->id,
            ['skip_duplicate' => false]
        );

        if (!empty($data['role_id'])) {
            $roleName = Role::find($data['role_id'])->name;
            $empleado->syncRoles([$roleName]);
            $empleado->update(['employee_department' => $roleName]);

            registrarAuditoria(
                'ROLE_CHANGE',
                'usuarios',
                $empleado->id,
                'Rol actualizado para usuario ID ' . $empleado->id . ': ' . $roleName,
                auth()->id() ?? $empleado->id,
                ['skip_duplicate' => false]
            );
        }

        return redirect()
            ->route('admin.empleados.index')
            ->with('success', 'Los datos de "' . $empleado->name . '" han sido actualizados.');
    }

    public function destroy(User $empleado)
    {
        abort_unless(auth()->user()->hasRole('administrador'), 403, 'No autorizado para eliminar.');

        $empleadoName = $empleado->name . ' ' . ($empleado->last_name ?? '');
        $empleadoId = $empleado->id;
        $empleado->delete();

        registrarAuditoria(
            'DELETE',
            'usuarios',
            $empleadoId,
            'Empleado eliminado: ' . trim($empleadoName) . ' (ID ' . $empleadoId . ')',
            auth()->id(),
            ['skip_duplicate' => false]
        );

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

        registrarAuditoria(
            'ROLE_CHANGE',
            'usuarios',
            $user->id,
            'Rol actualizado desde acción rápida para usuario ID ' . $user->id . ': ' . $data['role'],
            auth()->id() ?? $user->id,
            ['skip_duplicate' => false]
        );

        return back()->with('success', 'Rol asignado.');
    }
}
