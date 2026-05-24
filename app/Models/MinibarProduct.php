<?php

namespace App\Models;

use App\Traits\HasS3Image;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinibarProduct extends Model
{
    use HasFactory, HasS3Image;

    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'bebidas';

    protected $fillable = [
        'bebida_type_id',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'imagen',
    ];

    protected $appends = ['image_url'];

    protected $casts = [
        'precio' => 'float',
        'stock'  => 'integer',
    ];

    protected $imageField = 'imagen';

    public function type()
    {
        return $this->belongsTo(BebidaType::class, 'bebida_type_id');
    }

    public function compras()
    {
        return $this->belongsToMany(
            Compra::class,
            'compra_producto',
            'minibar_product_id',
            'compra_id'
        )
        ->withPivot('cantidad','precio_unitario')
        ->withTimestamps();
    }
}
