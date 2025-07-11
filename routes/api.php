<?php

use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [\App\Http\Controllers\Auth::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Auth::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Auth::class, 'logout'])->middleware('auth:sanctum');

Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index']);
Route::get('/products/likes', [\App\Http\Controllers\ProductController::class, 'getProductsByLikes'])->middleware('auth:sanctum');
Route::get('/products/{id}', [\App\Http\Controllers\ProductController::class, 'show']);
Route::get('/products/{id}/like', [\App\Http\Controllers\LikeController::class, 'show'])->middleware('auth:sanctum');
Route::post('/products/{id}/like', [\App\Http\Controllers\LikeController::class, 'store'])->middleware('auth:sanctum');
Route::get('/products/{id}/reviews', [\App\Http\Controllers\ReviewController::class, 'index']);
Route::post('/products/{id}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->middleware('auth:sanctum');
Route::put('/reviews/{id}', [\App\Http\Controllers\ReviewController::class, 'update'])->middleware('auth:sanctum');
Route::get('/orders/{id}', [\App\Http\Controllers\OrderController::class, 'show'])->middleware('auth:sanctum');
Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index'])->middleware('auth:sanctum');
Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'create'])->middleware('auth:sanctum');
Route::post('/yookassa/callback', [OrderController::class, 'callback'])->name('yookassa.callback');
Route::get('/orders/latest', [OrderController::class, 'latest'])->middleware('auth:sanctum');

Route::get('/search/', [\App\Http\Controllers\ProductController::class, 'search']);


Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index']);
Route::get('/categories/{slug}', [\App\Http\Controllers\ProductController::class, 'getProductsByCategory']);

Route::get('/carts', [\App\Http\Controllers\CartController::class, 'index'])->middleware('auth:sanctum');
Route::post('/carts', [\App\Http\Controllers\CartController::class, 'create'])->middleware('auth:sanctum');
Route::put('/carts', [\App\Http\Controllers\CartController::class, 'update'])->middleware('auth:sanctum');
Route::post('/carts/clear', [\App\Http\Controllers\CartController::class, 'clear'])->middleware('auth:sanctum');
Route::get('/carts/products', [\App\Http\Controllers\ProductController::class, 'getProductsByIds'])->middleware('auth:sanctum');
Route::delete('/carts/remove/{productId}', [\App\Http\Controllers\CartController::class, 'destroy'])->middleware('auth:sanctum');
