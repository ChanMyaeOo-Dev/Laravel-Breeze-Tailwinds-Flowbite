<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'order_number' => 'ORD-'.strtoupper(uniqid()),
            'subtotal' => fake()->randomFloat(2, 10, 500),
            'tax_amount' => fake()->randomFloat(2, 1, 50),
            'total_amount' => fake()->randomFloat(2, 11, 550),
            'status' => fake()->randomElement(['pending', 'preparing', 'ready', 'served', 'completed', 'cancelled']),
            'special_instructions' => fake()->optional()->sentence(),
        ];
    }
}
