<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController, ProductController, OrderController, CategoryController, TagController, CartController, StockMovementController, DashboardController};

Route::prefix('v1')->middleware('throttle:api')->group(function () {
    
    // Authentication routes (with stricter rate limit)
    Route::middleware('throttle:auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
    });
    
    // Public routes
    Route::apiResource('products', ProductController::class)->only(['index', 'show'])->names([
        'index' => 'api.products.index',
        'show' => 'api.products.show',
    ]);
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show'])->names([
        'index' => 'api.categories.index',
        'show' => 'api.categories.show',
    ]);
    Route::apiResource('tags', TagController::class)->only(['index', 'show'])->names([
        'index' => 'api.tags.index',
        'show' => 'api.tags.show',
    ]);
    
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
        
        // Dashboard metrics
        Route::get('dashboard/metrics', [DashboardController::class, 'metrics'])->name('api.dashboard.metrics');
        
        // Products (admin only)
        Route::apiResource('products', ProductController::class)->except(['index', 'show'])->names([
            'store' => 'api.products.store',
            'update' => 'api.products.update',
            'destroy' => 'api.products.destroy',
        ]);
        
        // Categories (admin only)
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show'])->names([
            'store' => 'api.categories.store',
            'update' => 'api.categories.update',
            'destroy' => 'api.categories.destroy',
        ]);
        Route::get('categories/{category}/products', [CategoryController::class, 'products'])->name('api.categories.products');
        
        // Tags (admin only)
        Route::apiResource('tags', TagController::class)->except(['index', 'show'])->names([
            'store' => 'api.tags.store',
            'update' => 'api.tags.update',
            'destroy' => 'api.tags.destroy',
        ]);
        
        // Orders
        Route::apiResource('orders', OrderController::class)->names([
            'index' => 'api.orders.index',
            'store' => 'api.orders.store',
            'show' => 'api.orders.show',
            'update' => 'api.orders.update',
            'destroy' => 'api.orders.destroy',
        ]);
        Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('api.orders.updateStatus');
        
        // Stock movements
        Route::get('stock-movements', [StockMovementController::class, 'index']);
        Route::post('stock-movements', [StockMovementController::class, 'store']);
        Route::get('products/{product}/stock-summary', [StockMovementController::class, 'summary']);
    });
});
