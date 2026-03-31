<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'stay_id',
        'folio_id',
        'invoice_number',
        'subtotal',
        'tax',
        'total',
        'discount',
        'is_paid',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    public function stay()
    {
        return $this->belongsTo(Stay::class);
    }

    public function folio()
    {
        return $this->belongsTo(Folio::class);
    }
}
