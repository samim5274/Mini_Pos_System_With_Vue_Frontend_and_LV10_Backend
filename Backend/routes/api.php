<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\Login\LoginController;
use App\Http\Controllers\Api\Cart\CartController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Product\ProductController;

// ======================
// Public Routes
// ======================
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register']);

// ======================
// Protected Routes
// ======================
Route::middleware('auth:sanctum')->group(function () {

    // Common Routes
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', [UserController::class, 'user']);

});
