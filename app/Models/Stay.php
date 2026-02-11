<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stay extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'room_id',
        'guest_id',
        'status',
        'arrival_at',
        'departure_at',
        'actual_check_in_at',
        'actual_check_out_at',
        'adults',
        'children',
        'rate_plan',
        'daily_rate',
        'notes',
    ];

    protected $casts = [
        'arrival_at' => 'datetime',
        'departure_at' => 'datetime',
        'actual_check_in_at' => 'datetime',
        'actual_check_out_at' => 'datetime',
        'daily_rate' => 'decimal:2',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'reservation_id');
    }

    public function folios()
    {
        return $this->hasMany(Folio::class);
    }
}
