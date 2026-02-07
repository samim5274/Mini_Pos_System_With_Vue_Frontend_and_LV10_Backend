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
    Route::get('/users', [UserController::class, 'users']);

    Route::get('/products', [ProductController::class, 'products']);
    Route::post('/create-product', [ProductController::class, 'store']);
    Route::delete('/delete-product/{id}', [ProductController::class, 'delete']);
    Route::get('/edit-product/{id}', [ProductController::class, 'edit']);
    Route::post('/update-product/{id}', [ProductController::class, 'update']);

    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/add', [CartController::class, 'addToCart']);
        Route::delete('/remove-item/{reg}/{id}', [CartController::class, 'removeItem']);
        Route::post('/qty-update/{reg}/{product_id}', [CartController::class, 'updateQty']);
    });

    Route::prefix('order')->group(function () {
        Route::post('/confirm', [OrderController::class, 'confirmOrder']);
    });

});
