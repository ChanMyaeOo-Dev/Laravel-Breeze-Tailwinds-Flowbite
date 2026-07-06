<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SwitchAccountController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('/');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Restaurants (admin only)
    Route::middleware('is_admin')->group(function () {
        Route::resource('restaurants', RestaurantController::class);
    });

    Route::resource('menus', MenuController::class);
    Route::resource('menu-categories', MenuCategoryController::class);

    // Orders
    Route::resource('orders', OrderController::class);
    Route::resource('orders.order-items', OrderItemController::class)->except(['index', 'show', 'create', 'edit']);

    // Switch Account
    Route::get('switch-account', [SwitchAccountController::class, 'index'])->name('switch-account')->middleware('is_admin');
    Route::get('switch-account/{restaurant}/login', [SwitchAccountController::class, 'showLogin'])->name('switch-account.show-login')->middleware('is_admin');
    Route::post('switch-account/{restaurant}', [SwitchAccountController::class, 'switch'])->name('switch-account.switch')->middleware('is_admin');
});
