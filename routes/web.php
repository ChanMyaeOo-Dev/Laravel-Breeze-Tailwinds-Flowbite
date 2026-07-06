<?php

use App\Http\Controllers\PublicOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/tables/{uuid}/order', [PublicOrderController::class, 'showOrderForm'])->name('public.order.form');
Route::post('/tables/{uuid}/order', [PublicOrderController::class, 'storeOrder'])->name('public.order.store');
Route::get('/tables/{uuid}/order/confirmation', [PublicOrderController::class, 'confirmation'])->name('public.order.confirmation');

require __DIR__.'/admin.php';
require __DIR__.'/auth.php';
