<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
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
            'name' => fake()->name,
            'slug' => Str::slug(fake()->name),
            'type' => ['Breakfast', 'Lunch', 'Dinner', 'Snack', 'Dessert', 'Drink'][rand(0, 5)],
            'price' => fake()->numberBetween(1000, 10000),
            'description' => fake()->paragraph(3),
            'image' => null,
            'status' => true,
        ];
    }
}
