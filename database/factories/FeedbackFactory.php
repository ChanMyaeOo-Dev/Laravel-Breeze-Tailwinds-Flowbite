<?php

namespace Database\Factories;

use App\Models\Feedback;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feedback>
 */
class FeedbackFactory extends Factory
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
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence(12),
        ];
    }
}
