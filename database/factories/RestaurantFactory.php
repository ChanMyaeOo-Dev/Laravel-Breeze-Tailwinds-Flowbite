<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Restaurant>
 */
class RestaurantFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Restaurant',
            'username' => fake()->unique()->userName(),
            'password' => static::$password ??= \Illuminate\Support\Facades\Hash::make('password'),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'logo_url' => null,
            'opening_time' => '09:00:00',
            'closing_time' => '22:00:00',
            'is_active' => true,
        ];
    }
}
