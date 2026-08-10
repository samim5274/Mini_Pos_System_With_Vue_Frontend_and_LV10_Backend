<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Order\OrderController;

Route::get('/', function () {
    return view('welcome');
});

// Route::post('/pay-via-ajax', [OrderController::class, 'payViaAjax']);
Route::post('/success', [OrderController::class, 'success']);
Route::post('/fail', [OrderController::class, 'fail']);
Route::post('/cancel', [OrderController::class, 'cancel']);
Route::post('/ipn', [OrderController::class, 'ipn']);
