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
