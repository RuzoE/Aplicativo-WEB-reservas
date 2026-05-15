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
        'precio' => 'float',
        'stock'  => 'integer',
    ];

    /**
     * Obtiene la URL firmada de la imagen desde S3 (válida 2 horas).
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->imagen) {
            return Storage::disk('s3')->temporaryUrl(
                $this->imagen,
                now()->addHours(2)
            );
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
