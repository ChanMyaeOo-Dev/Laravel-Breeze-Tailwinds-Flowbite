<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Restaurant::factory()->create([
            'name' => 'Test Restaurant',
            'username' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        \App\Models\Restaurant::factory(5)->create();
    }
}
