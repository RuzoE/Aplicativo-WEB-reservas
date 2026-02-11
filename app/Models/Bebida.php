<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bebida extends Model
{
    // Si tu tabla no se llama "bebidas", especifica:
    // protected $table = 'minibar_products';

    protected $fillable = [
        'nombre',
        'precio',
        'stock',
        'imagen',
        'bebida_type_id',
    ];

    public function type()
    {
        return $this->belongsTo(BebidaType::class, 'bebida_type_id');
    }
}
