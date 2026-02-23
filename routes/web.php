<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\PublicCartController;
use App\Http\Controllers\Web\{ProductController, OrderController, CategoryController, TagController, RoleController, UserRoleController, PermissionController};

Route::get('/', [StorefrontController::class, 'index'])->name('home');
Route::get('/produto/{id}', [StorefrontController::class, 'show'])->name('storefront.product');

Route::prefix('cart')->group(function () {
    Route::get('/', [PublicCartController::class, 'show'])->name('cart.show');
    Route::post('/items', [PublicCartController::class, 'addItem'])->name('cart.add');
    Route::put('/items/{cartItem}', [PublicCartController::class, 'updateItem'])->name('cart.update');
    Route::delete('/items/{cartItem}', [PublicCartController::class, 'removeItem'])->name('cart.remove');
    Route::post('/merge', [PublicCartController::class, 'mergeOnLogin'])->name('cart.merge');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
    
    Route::get('/meus-pedidos', [App\Http\Controllers\MyOrdersController::class, 'index'])->name('my-orders.index');
    Route::get('/meus-pedidos/{id}', [App\Http\Controllers\MyOrdersController::class, 'show'])->name('my-orders.show');
    
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
    
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class)->except(['create', 'store', 'destroy']);
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus')->middleware('permission:orders.update');
    Route::resource('categories', CategoryController::class);
    Route::resource('tags', TagController::class);
    
    Route::resource('roles', RoleController::class)->middleware('permission:roles.view');
    Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'destroy'])->middleware('permission:permissions.view');
    Route::resource('users', UserRoleController::class)->only(['index'])->middleware('permission:users.view');
    Route::get('users/{user}/edit-roles', [UserRoleController::class, 'edit'])->name('users.edit-roles')->middleware('permission:roles.update');
    Route::put('users/{user}/roles', [UserRoleController::class, 'update'])->name('users.update-roles')->middleware('permission:roles.update');
});

require __DIR__.'/settings.php';
