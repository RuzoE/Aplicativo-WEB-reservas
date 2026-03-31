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
}
