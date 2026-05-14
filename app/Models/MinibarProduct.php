<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MinibarProduct extends Model
{
    use HasFactory;

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
        'precio' => 'float',  // "3000.00"
        'stock'  => 'integer',
    ];

    /**
     * Obtiene la URL completa de la imagen desde S3.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->imagen) {
            return Storage::disk('s3')->url($this->imagen);
        }
        return asset('images/no-image.png');
    }

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
