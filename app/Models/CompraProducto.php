<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompraProducto extends Model
{
    use HasFactory;

    protected $table = 'compra_producto';

    protected $fillable = [
        'compra_id',
        'minibar_product_id',
        'cantidad',
        'precio_unitario',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function product()
    {
        return $this->belongsTo(MinibarProduct::class, 'minibar_product_id');
    }
}

