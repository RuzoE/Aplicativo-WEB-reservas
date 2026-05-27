<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\MaintenanceOrder;

class Order extends Model {

    use HasFactory;

    const STATUS_PENDIENTE = 'pendiente';
    const STATUS_ANTICIPO_PAGADO = 'anticipo_pagado';
    const STATUS_PENDIENTE_PAGO = 'pendiente_pago';
    const STATUS_RESERVA_PREVIA = 'reserva_previa';
    const STATUS_RESERVA_ASIGNADA = 'reserva_asignada';
    const STATUS_OCUPADA = 'ocupada';
    const STATUS_FINALIZADA = 'finalizada';

    protected $fillable = [
        'nombre_cliente',
        'check_in',
        'check_out',
        'room_id',
        'room_number',
        'room_type_id',
        'user_id',
        'status',
        'payment_token',
        'down_payment_amount',
        'is_paid',
    ];

    protected $appends = ['stayDays', 'total_amount'];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'is_paid' => 'boolean',
        'down_payment_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            $order->payment_token = \Illuminate\Support\Str::random(40);
            if ($order->status === null) {
                $order->status = 'pendiente';
            }
        });
    }

    public function getTotalAmountAttribute()
    {
        $price = $this->room->price ?? 0;

        // Fallback: if room is null but we have room_type_id, get the price from the room category
        if ($price == 0 && $this->room_type_id) {
            $price = Room::where('room_type_id', $this->room_type_id)->value('price') ?? 0;
        }

        return (float)$price * (int)$this->stayDays;
    }

    public function room(): BelongsTo {

        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function user(): BelongsTo {

        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function stays()
    {
        return $this->hasMany(Stay::class, 'reservation_id');
    }

    public function roomType(): BelongsTo {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function getStayDaysAttribute() {

        return $this->check_in->diffInDays($this->check_out);
    }
}
