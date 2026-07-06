<?php

namespace Database\Seeders;

use App\Models\Menu;
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

        Restaurant::factory(3)
            ->for($admin)
            ->has(Menu::factory(5))
            ->create();

        Restaurant::factory(3)
            ->for($shan_ma_lay)
            ->has(Menu::factory(5))
            ->create();

        Restaurant::factory(3)
            ->has(Menu::factory(5))
            ->create();
    }
}
