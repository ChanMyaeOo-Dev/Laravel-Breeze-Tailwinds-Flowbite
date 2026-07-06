<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

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
            'user_id' => User::factory(),
            'name' => fake()->company().' Restaurant',
            'username' => fake()->unique()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'logo_url' => null,
            'opening_time' => '09:00:00',
            'closing_time' => '22:00:00',
            'is_active' => true,
        ];
    }
}
