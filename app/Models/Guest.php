<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'document_type',
        'document_number',
        'email',
        'phone',
        'country',
        'notes',
    ];

    public function stays()
    {
        return $this->hasMany(Stay::class);
    }
}
