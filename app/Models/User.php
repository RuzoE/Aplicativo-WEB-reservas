<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Spatie: usamos el guard 'web'
     */
    protected string $guard_name = 'web';

    /**
     * Campos asignables
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'password',
        'is_employee',
        'employee_department',
    ];

    /**
     * Ocultos en serialización
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     * - 'password' => 'hashed' aplica hash automático al asignar
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_employee'       => 'boolean',
    ];

    /**
     * Relación: un usuario tiene muchos pedidos
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    /**
     * Relación: un usuario tiene muchas estancias (reservas)
     */
    public function stays(): HasMany
    {
        return $this->hasMany(Stay::class, 'user_id', 'id');
    }

    /**
     * Relación: un usuario tiene muchas sesiones
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class, 'user_id', 'id');
    }

    /**
     * Relación: un usuario tiene muchas actividades
     */
    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class, 'user_id', 'id');
    }

    /**
     * Obtener el rol principal del usuario
     * Si es empleado, retorna el rol del empleado; si no, retorna "Invitado"
     */
    public function getDisplayRoleAttribute(): string
    {
        // Si el usuario tiene un rol explícito asignado
        $role = $this->roles()->first();
        
        if ($role) {
            return ucfirst($role->name);
        }

        // Si es empleado pero sin rol específico (fallback)
        if ($this->is_employee && $this->employee_department) {
            return ucfirst($this->employee_department);
        }

        // Por defecto: Invitado
        return 'Invitado';
    }

    /**
     * Obtener el estado formateado en español
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'blocked' => 'Bloqueado',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener el color del estado para badges
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            'blocked' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verificar si el usuario está inactivo
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Verificar si el usuario está bloqueado
     */
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    /**
     * Verificar si el usuario es un empleado del hotel
     */
    public function isEmployee(): bool
    {
        return $this->is_employee || $this->hasRole(['reservas', 'minibar', 'recepcion', 'mantenimiento', 'administrador']);
    }

    /**
     * Verificar si el usuario es un invitado (cliente)
     */
    public function isGuest(): bool
    {
        return !$this->isEmployee();
    }

    /**
     * Activar usuario
     */
    public function activate(): bool
    {
        return $this->update(['status' => 'active']);
    }

    /**
     * Desactivar usuario
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => 'inactive']);
    }

    /**
     * Bloquear usuario
     */
    public function block(): bool
    {
        return $this->update(['status' => 'blocked']);
    }

    /**
     * Marcar como empleado
     */
    public function markAsEmployee(string $department = null): bool
    {
        return $this->update([
            'is_employee' => true,
            'employee_department' => $department,
        ]);
    }

    /**
     * Marcar como invitado/cliente
     */
    public function markAsGuest(): bool
    {
        return $this->update([
            'is_employee' => false,
            'employee_department' => null,
        ]);
    }

    /**
     * Obtener sesiones activas
     */
    public function getActiveSessions()
    {
        return $this->sessions()->active()->get();
    }

    /**
     * Obtener actividades recientes
     */
    public function getRecentActivities($days = 30, $limit = 50)
    {
        return $this->activities()
            ->recent($days)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Último acceso formateado
     */
    public function getLastLoginFormatted(): string
    {
        if (!$this->last_login_at) {
            return 'Nunca';
        }
        return $this->last_login_at->format('d/m/Y H:i');
    }
}
