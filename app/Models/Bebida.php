<?php

namespace App\Models;

use App\Traits\HasS3Image;
use Illuminate\Database\Eloquent\Model;

class Bebida extends Model
{
    use HasS3Image;

    // Si tu tabla no se llama "bebidas", especifica:
    // protected $table = 'minibar_products';

    protected $fillable = [
        'nombre',
        'precio',
        'stock',
        'imagen',
        'bebida_type_id',
    ];

    protected $appends = ['image_url'];

    protected $imageField = 'imagen';

    public function type()
    {
        return $this->belongsTo(BebidaType::class, 'bebida_type_id');
    }
}
