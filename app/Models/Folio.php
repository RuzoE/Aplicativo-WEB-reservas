<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folio extends Model
{
    use HasFactory;

    protected $fillable = [
        'stay_id',
        'number',
        'status',
        'currency',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function stay()
    {
        return $this->belongsTo(Stay::class);
    }

    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
