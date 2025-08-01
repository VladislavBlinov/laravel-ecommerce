<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
});
Route::get('/search/', SearchController::class);

Route::prefix('likes')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [LikeController::class, 'index']);
    Route::get('/{product}', [LikeController::class, 'show']);
    Route::post('/{product}', [LikeController::class, 'store']);
});

Route::prefix('reviews')->group(function () {
    Route::get('/{productId}', [ReviewController::class, 'index']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{productId}', [ReviewController::class, 'store']);
        Route::put('/{reviewId}', [ReviewController::class, 'update']);
    });
});

Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/{orderId}', [OrderController::class, 'show']);
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'create']);
    Route::get('/latest', [OrderController::class, 'latest']);
});
Route::post('/yookassa/callback', [OrderController::class, 'callback'])->name('yookassa.callback');

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{categorySlug}', [CategoryController::class, 'getCategoryProducts']);
});

Route::prefix('carts')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/', [CartController::class, 'create']);
    Route::put('/', [CartController::class, 'update']);
    Route::delete('/', [CartController::class, 'clear']);
    Route::delete('/{productId}', [CartController::class, 'destroy']);

});
