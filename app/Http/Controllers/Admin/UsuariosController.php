<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserSession;
use App\Rules\AllowedEmailDomain;
use App\Rules\AlphaSpace;
use App\Rules\PhoneNumberByPrefix;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UsuariosController extends Controller
{
    /**
     * Listar usuarios del sistema
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'in:active,inactive,blocked'],
            'per_page' => ['nullable', 'integer', 'in:10,25,50,100'],
            'sort' => ['nullable', 'string', 'in:name,email,last_login_at,created_at'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 25);
        $sort = $validated['sort'] ?? 'created_at';
        $direction = $validated['direction'] ?? 'desc';

        $query = User::with('roles')
            ->where('id', '!=', auth()->id()) // Excluir el usuario actual
            ->when(!empty($validated['search']), function ($q) use ($validated) {
                $search = $validated['search'];
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            })
            ->when(!empty($validated['role']), function ($q) use ($validated) {
                $q->whereHas('roles', function ($roleQuery) use ($validated) {
                    $roleQuery->where('name', $validated['role']);
                });
            })
            ->when(!empty($validated['status']), function ($q) use ($validated) {
                $q->where('status', $validated['status']);
            })
            ->orderBy($sort, $direction);

        $usuarios = $query->paginate($perPage)->withQueryString();

        // Obtener roles disponibles
        $roles = Role::all(['id', 'name']);
        
        // Actividades recientes del sistema
        $recentActivities = UserActivity::with('user:id,name,email')
            ->recent(7)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'recentActivities'));
    }

    /**
     * Mostrar formulario de creación (deshabilitado)
     * Los usuarios se crean automáticamente desde Empleados
     */
    public function create()
    {
        return redirect()->route('admin.empleados.index')
            ->with('info', 'Los usuarios se crean automáticamente al registrar empleados. Dirígete a la sección de Empleados.');
    }

    /**
     * Guardar nuevo usuario (deshabilitado)
     * Los usuarios se crean automáticamente desde Empleados
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.empleados.index')
            ->with('info', 'Los usuarios se crean automáticamente al registrar empleados. Dirígete a la sección de Empleados.');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(User $usuario)
    {
        $this->authorize('update', $usuario);
        $roles = Role::whereIn('name', ['reservas', 'minibar', 'recepcion', 'mantenimiento'])->get();
        $currentRole = $usuario->roles()->first();
        return view('admin.usuarios.edit', compact('usuario', 'roles', 'currentRole'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $usuario)
    {
        $this->authorize('update', $usuario);

        $data = $request->validate([
            'name' => ['required', new AlphaSpace, 'max:100'],
            'last_name' => ['nullable', new AlphaSpace, 'max:100'],
            'email' => ['required', 'email', 'max:150', new AllowedEmailDomain(), 'unique:users,email,' . $usuario->id],
            'phone' => ['nullable', new PhoneNumberByPrefix()],
            'role_id' => ['nullable', 'exists:roles,id'],
            'status' => ['nullable', 'string', 'in:active,inactive,blocked'],
        ]);

        $oldData = $usuario->only(['name', 'last_name', 'email', 'status']);
        $oldRole = $usuario->roles()->first()?->name;

        $usuario->update([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'status' => $data['status'] ?? $usuario->status,
        ]);

        // Si cambia el rol
        if (!empty($data['role_id'])) {
            $role = Role::find($data['role_id']);
            if ($oldRole && $oldRole !== $role->name) {
                $usuario->syncRoles([$role->name]);
                registrarAuditoria(
                    'ROLE_CHANGE',
                    'usuarios',
                    $usuario->id,
                    "Rol cambiado de {$oldRole} a {$role->name}",
                    auth()->id()
                );
            }
        }

        // Registrar cambios
        $changes = [];
        foreach (['name', 'last_name', 'email', 'status'] as $field) {
            if ($oldData[$field] !== $usuario->{$field}) {
                $changes[] = "{$field}: {$oldData[$field]} → {$usuario->{$field}}";
            }
        }

        if (!empty($changes)) {
            registrarAuditoria(
                'UPDATE',
                'usuarios',
                $usuario->id,
                'Usuario actualizado: ' . implode(', ', $changes),
                auth()->id()
            );
        }

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $usuario)
    {
        $this->authorize('delete', $usuario);

        $nombre = $usuario->name . ' ' . $usuario->last_name;
        $email = $usuario->email;

        // Soft delete o hard delete según preferencia
        registrarAuditoria(
            'DELETE',
            'usuarios',
            $usuario->id,
            "Usuario eliminado: {$nombre} ({$email})",
            auth()->id()
        );

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Cambiar contraseña por el usuario
     */
    public function changePassword(User $usuario)
    {
        $this->authorize('changePassword', $usuario);
        return view('admin.usuarios.change-password', compact('usuario'));
    }

    /**
     * Procesar cambio de contraseña
     */
    public function updatePassword(Request $request, User $usuario)
    {
        $this->authorize('changePassword', $usuario);

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
        ]);

        $usuario->update(['password' => $data['password']]);

        registrarAuditoria(
            'PASSWORD_CHANGE',
            'usuarios',
            $usuario->id,
            'Contraseña cambiada por el usuario',
            auth()->id()
        );

        $this->logActivity(
            $usuario->id,
            'password_change',
            'Contraseña modificada',
            'success'
        );

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Contraseña actualizada correctamente.');
    }

    /**
     * Resetear contraseña por administrador
     */
    public function resetPassword(Request $request, User $usuario)
    {
        $this->authorize('resetPassword', $usuario);

        $tempPassword = $this->generateTemporaryPassword();
        $usuario->update(['password' => $tempPassword]);

        registrarAuditoria(
            'PASSWORD_RESET',
            'usuarios',
            $usuario->id,
            'Contraseña reseteada por administrador',
            auth()->id()
        );

        $this->logActivity(
            $usuario->id,
            'password_reset',
            'Contraseña reseteada por administrador',
            'success'
        );

        return response()->json([
            'success' => true,
            'message' => 'Contraseña temporal generada',
            'temporary_password' => $tempPassword,
        ]);
    }

    /**
     * Cambiar estado del usuario
     */
    public function toggleStatus(Request $request, User $usuario)
    {
        $this->authorize('toggleStatus', $usuario);

        $data = $request->validate([
            'status' => ['required', 'string', 'in:active,inactive,blocked'],
        ]);

        $oldStatus = $usuario->status;
        $usuario->update(['status' => $data['status']]);

        registrarAuditoria(
            'STATUS_CHANGE',
            'usuarios',
            $usuario->id,
            "Estado cambiado de {$oldStatus} a {$data['status']}",
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'new_status' => $usuario->status_label,
        ]);
    }

    /**
     * Ver historial de actividad del usuario
     */
    public function activity(User $usuario)
    {
        $this->authorize('viewActivities', $usuario);

        $activities = $usuario->activities()
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.usuarios.activity', compact('usuario', 'activities'));
    }

    /**
     * Ver sesiones activas del usuario
     */
    public function sessions(User $usuario)
    {
        $this->authorize('viewSessions', $usuario);

        $activeSessions = $usuario->sessions()->active()->get();
        $inactiveSessions = $usuario->sessions()->inactive()->get();

        return view('admin.usuarios.sessions', compact('usuario', 'activeSessions', 'inactiveSessions'));
    }

    /**
     * Cerrar sesión remota
     */
    public function logoutRemote(Request $request, User $usuario)
    {
        $this->authorize('logoutRemote', $usuario);

        $data = $request->validate([
            'session_id' => ['required', 'exists:user_sessions,id'],
        ]);

        $session = UserSession::findOrFail($data['session_id']);

        if ($session->user_id !== $usuario->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $session->delete();

        registrarAuditoria(
            'LOGOUT_REMOTE',
            'usuarios',
            $usuario->id,
            'Sesión cerrada remotamente desde ' . $session->device_name,
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente',
        ]);
    }

    /**
     * Generar contraseña temporal segura
     */
    private function generateTemporaryPassword(): string
    {
        $length = 12;
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }

    /**
     * Registrar actividad de usuario (helper)
     */
    private function logActivity($userId, $action, $description, $status = 'success', $metadata = []): void
    {
        UserActivity::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_name' => $this->getDeviceName(),
            'status' => $status,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Obtener nombre del dispositivo desde user agent
     */
    private function getDeviceName(): string
    {
        $ua = request()->userAgent();
        
        if (str_contains($ua, 'Windows')) {
            return 'Windows';
        } elseif (str_contains($ua, 'Macintosh')) {
            return 'macOS';
        } elseif (str_contains($ua, 'Linux')) {
            return 'Linux';
        } elseif (str_contains($ua, 'iPhone')) {
            return 'iPhone';
        } elseif (str_contains($ua, 'Android')) {
            return 'Android';
        }
        
        return 'Desconocido';
    }
}
