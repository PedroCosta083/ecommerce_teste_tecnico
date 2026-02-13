<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController, ProductController, OrderController, CategoryController, TagController, CartController, StockMovementController};

Route::prefix('v1')->middleware('throttle:api')->group(function () {
    
    // Authentication routes (with stricter rate limit)
    Route::middleware('throttle:auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
    });
    
    // Public routes
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
    Route::apiResource('tags', TagController::class)->only(['index', 'show']);
    
    // Cart routes (public)
    Route::get('cart', [CartController::class, 'show']);
    Route::post('cart/items', [CartController::class, 'addItem']);
    Route::put('cart/items/{cartItem}', [CartController::class, 'updateItem']);
    Route::delete('cart/items/{cartItem}', [CartController::class, 'removeItem']);
    Route::delete('cart/{cart}/clear', [CartController::class, 'clear']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        
        // Products (admin only)
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        
        // Categories (admin only)
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
        Route::get('categories/{category}/products', [CategoryController::class, 'products']);
        
        // Tags (admin only)
        Route::apiResource('tags', TagController::class)->except(['index', 'show']);
        
        // Orders
        Route::apiResource('orders', OrderController::class);
        Route::put('orders/{order}/status', [OrderController::class, 'updateStatus']);
        
        // Stock movements
        Route::get('stock-movements', [StockMovementController::class, 'index']);
        Route::post('stock-movements', [StockMovementController::class, 'store']);
        Route::get('products/{product}/stock-summary', [StockMovementController::class, 'summary']);
    });
});
