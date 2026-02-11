<?php

namespace Database\Factories;

use App\Models\BebidaType;
use Illuminate\Database\Eloquent\Factories\Factory;

class BebidaTypeFactory extends Factory
{
    protected $model = BebidaType::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->word(),
        ];
    }
}
