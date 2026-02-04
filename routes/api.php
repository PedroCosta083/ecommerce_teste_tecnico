<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\StockMovementController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('categories', CategoryController::class);
Route::apiResource('tags', TagController::class);

// Cart routes
Route::get('cart', [CartController::class, 'show']);
Route::post('cart/items', [CartController::class, 'addItem']);
Route::put('cart/items/{cartItem}', [CartController::class, 'updateItem']);
Route::delete('cart/items/{cartItem}', [CartController::class, 'removeItem']);
Route::delete('cart/{cart}/clear', [CartController::class, 'clear']);

// Stock movement routes
Route::get('stock-movements', [StockMovementController::class, 'index']);
Route::post('stock-movements', [StockMovementController::class, 'store']);
Route::get('products/{product}/stock-summary', [StockMovementController::class, 'summary']);