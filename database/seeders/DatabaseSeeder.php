<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['name' => 'Admin'],
            [
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        $shan_ma_lay = User::firstOrCreate(
            ['name' => 'Shan Ma Lay'],
            [
                'email' => 'shan@gmail.com', // Added a valid email format if required by your DB
                'password' => Hash::make('password'),
            ]
        );

        // If you want to ensure the restaurant exists for the admin:
        $restaurant = Restaurant::firstOrCreate(
            ['user_id' => $admin->id], // Assuming the foreign key is user_id
            [
                // Add other default restaurant attributes here if needed,
                // or use the factory to generate them:
                'name' => 'Admin Restaurant',
            ]
        );

        $this->call([
            FeedbackSeeder::class,
        ]);
    }
}
