<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\RestaurantTable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RestaurantTable>
 */
class RestaurantTableFactory extends Factory
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
            'table_number' => 'T-'.$this->faker->unique()->numberBetween(1, 200),
            'qr_code' => null,
            'qr_code_image' => null,
            'seating_capacity' => $this->faker->randomElement([2, 4, 6, 8, 10]),
            'section' => $this->faker->optional()->randomElement(['Indoor', 'Outdoor', 'Patio', 'VIP', 'Bar']),
            'status' => 'available',
        ];
    }
}
