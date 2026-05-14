<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Ödeme formunu gösteren rota
Route::get('/', [PaymentController::class, 'showForm'])->name('payment.form');

// Ödeme formundan gelen veriyi işleyen rota
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');

// Bankadan dönüş rotası (Success ve Failure buraya düşecek)
Route::any('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/test-config', function () {
    dd(config('payment.finansbank'));
});

