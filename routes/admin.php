<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('/');
    Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Restaurants
    Route::resource('restaurants', App\Http\Controllers\RestaurantController::class);
    Route::resource('menus', App\Http\Controllers\MenuController::class);

    // Menus
});
