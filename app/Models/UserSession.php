<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token_name',
        'device_name',
        'ip_address',
        'user_agent',
        'last_activity_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Una sesión pertenece a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para sesiones activas (últimas 24 horas)
     */
    public function scopeActive($query)
    {
        return $query->where('last_activity_at', '>=', now()->subDay());
    }

    /**
     * Scope para sesiones inactivas (más de 24 horas)
     */
    public function scopeInactive($query)
    {
        return $query->where('last_activity_at', '<', now()->subDay());
    }
}
