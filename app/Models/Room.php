<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Room extends Model {

    use HasFactory;

    const STATUS_DISPONIBLE = 'disponible';
    const STATUS_OCUPADA = 'ocupada';
    const STATUS_MANTENIMIENTO = 'mantenimiento';

    protected $fillable = [
        'total_room', // Numero de habitación
        'no_beds',
        'price',
        'image',
        'status',
        'desc',
        'room_type_id',
    ];

    protected $casts = [
        // 'status' => 'boolean' // Ya no es booleano
    ];

    protected $appends = ['room_number'];

    public function getRoomNumberAttribute()
    {
        return $this->total_room;
    }

    public function roomtype(): BelongsTo {
        return $this->belongsTo(RoomType::class, 'room_type_id', 'id');
    }

    public function orders(): HasMany {
        return $this->hasMany(Order::class, 'room_id', 'id');
    }

    public function maintenanceOrders(): HasMany {
        return $this->hasMany(MaintenanceOrder::class, 'room_id', 'id');
    }
}
