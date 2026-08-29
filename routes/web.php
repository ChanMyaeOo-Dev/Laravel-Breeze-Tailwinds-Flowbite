<?php

use App\Http\Controllers\API\MlAnalysisController;
use App\Http\Controllers\PublicOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/tables/{uuid}/order', [PublicOrderController::class, 'showOrderForm'])->name('public.order.form');
Route::post('/tables/{uuid}/order', [PublicOrderController::class, 'storeOrder'])->name('public.order.store');
Route::get('/tables/{uuid}/order/confirmation', [PublicOrderController::class, 'confirmation'])->name('public.order.confirmation');

require __DIR__.'/admin.php';
require __DIR__.'/auth.php';

Route::prefix('ml')->group(function () {
    Route::post('/analyze/{feedbackId}', [MlAnalysisController::class, 'analyzeSingle']);
    Route::get('/analytics', [MlAnalysisController::class, 'analytics']);
    Route::get('/feedback', [MlAnalysisController::class, 'feedbackWithAnalysis']);
});

// php -d memory_limit=1G artisan ml:train-sentiment --test-size=0.2
// pa feedback:analyze-ml --batch-size=200
