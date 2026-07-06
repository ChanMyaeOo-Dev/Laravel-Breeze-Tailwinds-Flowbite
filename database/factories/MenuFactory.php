<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'menu_category_id' => MenuCategory::factory(),
            'name' => fake()->name,
            'slug' => Str::slug(fake()->name),
            'price' => fake()->numberBetween(1000, 10000),
            'description' => fake()->paragraph(3),
            'image' => null,
            'status' => true,
        ];
    }
}
