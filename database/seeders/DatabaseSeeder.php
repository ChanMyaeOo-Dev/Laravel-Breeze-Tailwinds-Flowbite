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
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $shan_ma_lay = User::factory()->create([
            'name' => 'Shan Ma Lay',
            'email' => 'shan',
            'password' => Hash::make('password'),
        ]);

        $adminRestaurants = Restaurant::factory(3)->for($admin)->create();
        foreach ($adminRestaurants as $restaurant) {
            $categories = MenuCategory::factory(3)->for($restaurant)->create();
            foreach ($categories as $category) {
                Menu::factory(5)->for($restaurant)->for($category)->create();
            }
        }

        $shanRestaurants = Restaurant::factory(3)->for($shan_ma_lay)->create();
        foreach ($shanRestaurants as $restaurant) {
            $categories = MenuCategory::factory(3)->for($restaurant)->create();
            foreach ($categories as $category) {
                Menu::factory(5)->for($restaurant)->for($category)->create();
            }
        }

        $unlinkedRestaurants = Restaurant::factory(3)->create();
        foreach ($unlinkedRestaurants as $restaurant) {
            $categories = MenuCategory::factory(3)->for($restaurant)->create();
            foreach ($categories as $category) {
                Menu::factory(5)->for($restaurant)->for($category)->create();
            }
        }
    }
}
