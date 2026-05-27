<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_cliente' => $this->faker->name(),
            'check_in' => now()->addDays(1)->toDateString(),
            'check_out' => now()->addDays(3)->toDateString(),
            'room_number' => $this->faker->numberBetween(101, 310),
            'status' => Order::STATUS_PENDIENTE,
            'down_payment_amount' => 0,
            'payment_token' => \Illuminate\Support\Str::random(32),
        ];
    }
}
