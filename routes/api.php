<?php

use App\Http\Controllers\Api\BroadcastAuthController;
use App\Http\Controllers\Api\KitchenAuthController;
use App\Http\Controllers\Api\KitchenOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/kitchen/login', [KitchenAuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/broadcasting/auth', [BroadcastAuthController::class, 'authorize']);
    Route::post('/kitchen/logout', [KitchenAuthController::class, 'logout']);

    Route::get('/kitchen/orders', [KitchenOrderController::class, 'index']);
    Route::get('/kitchen/orders/{order}', [KitchenOrderController::class, 'show']);
    Route::patch('/kitchen/orders/{order}/status', [KitchenOrderController::class, 'updateStatus']);
    Route::patch('/kitchen/orders/{order}/items/{orderItem}/status', [KitchenOrderController::class, 'updateItemStatus']);
});
