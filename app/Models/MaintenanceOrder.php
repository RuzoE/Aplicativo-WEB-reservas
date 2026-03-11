<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class MaintenanceOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'room_number',
        'priority',
        'status',
        'description',
        'notes',
        'estimated_time',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relación
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'completada')
                     ->where('status', '!=', 'cancelada');
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgente');
    }
}
