<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BebidaType extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'es_alcoholica',
    ];

    protected $casts = [
        'es_alcoholica' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(MinibarProduct::class, 'bebida_type_id');
    }
}
