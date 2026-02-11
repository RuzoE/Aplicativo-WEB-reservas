<?php

namespace Database\Factories;

use App\Models\MinibarProduct;
use App\Models\BebidaType;
use Illuminate\Database\Eloquent\Factories\Factory;

class MinibarProductFactory extends Factory
{
    protected $model = MinibarProduct::class;

    public function definition(): array
    {
        return [
            'nombre'         => ucfirst($this->faker->unique()->words(2, true)),
            'precio'         => $this->faker->numberBetween(500, 5000),
            'stock'          => $this->faker->numberBetween(0, 100),
            'descripcion'    => $this->faker->optional()->sentence(),
            'bebida_type_id' => BebidaType::factory(), // crea el tipo si no existe
            // 'estado' => true, // <- QUITADO (no existe en DB)
        ];
    }
}
