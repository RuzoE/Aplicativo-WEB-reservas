<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';

    protected $fillable = [
        'user_id',
        'total',
        'estado',
        'metodo_pago',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productos()
    {
        return $this->belongsToMany(
            MinibarProduct::class,
            'compra_producto',
            'compra_id',
            'minibar_product_id'
        )
        ->withPivot('cantidad','precio_unitario')
        ->withTimestamps();
    }
}
