<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('/');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Restaurants
    Route::resource('restaurants', RestaurantController::class);
    Route::resource('menus', MenuController::class);
    Route::resource('menu-categories', MenuCategoryController::class);

    // Menus
});
