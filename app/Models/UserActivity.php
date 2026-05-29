<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'activity_type',
        'description',
        'ip_address',
        'user_agent',
        'device_name',
        'status',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'metadata' => 'json',
    ];

    /**
     * Una actividad pertenece a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para obtener actividades recientes
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope para filtrar por acción
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para obtener logins
     */
    public function scopeLogins($query)
    {
        return $query->where('action', 'login');
    }

    /**
     * Scope para obtener logouts
     */
    public function scopeLogouts($query)
    {
        return $query->where('action', 'logout');
    }

    /**
     * Scope para obtener cambios de contraseña
     */
    public function scopePasswordChanges($query)
    {
        return $query->where('action', 'password_change');
    }

    /**
     * Scope para obtener cambios de rol/permisos
     */
    public function scopePermissionChanges($query)
    {
        return $query->where('action', 'permission_change');
    }
}
