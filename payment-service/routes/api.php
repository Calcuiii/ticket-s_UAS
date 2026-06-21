<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::middleware('jwt.verify')->prefix('payments')->group(function () {
    Route::post('/', [PaymentController::class, 'store']);
    Route::get('/history', [PaymentController::class, 'history']);
    Route::get('/{id}', [PaymentController::class, 'show']);
});