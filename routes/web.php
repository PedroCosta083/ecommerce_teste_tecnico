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
    
    // Resources usam authorizeResource() nos controllers
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('tags', TagController::class);
    Route::resource('orders', OrderController::class)->except(['create', 'store', 'destroy']);
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    
    // Reports - requer permissão products.view
    Route::prefix('reports')->name('reports.')->middleware('can:products.view')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\ReportController::class, 'index'])->name('index');
        Route::get('/low-stock', [App\Http\Controllers\Web\ReportController::class, 'lowStock'])->name('low-stock');
        Route::get('/stock-movements', [App\Http\Controllers\Web\ReportController::class, 'stockMovements'])->name('stock-movements');
        Route::get('/revenue', [App\Http\Controllers\Web\ReportController::class, 'revenue'])->name('revenue');
    });
    
    // Notifications - requer permissão products.view
    Route::prefix('notifications')->name('notifications.')->middleware('can:products.view')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [App\Http\Controllers\Web\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [App\Http\Controllers\Web\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/unread-count', [App\Http\Controllers\Web\NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });
    
    // Roles & Permissions - requer permissões específicas
    Route::resource('roles', RoleController::class)->middleware('can:roles.view');
    Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'destroy'])->middleware('can:permissions.view');
    Route::resource('users', UserRoleController::class)->only(['index'])->middleware('can:users.view');
    Route::get('users/{user}/edit-roles', [UserRoleController::class, 'edit'])->name('users.edit-roles')->middleware('can:roles.update');
    Route::put('users/{user}/roles', [UserRoleController::class, 'update'])->name('users.update-roles')->middleware('can:roles.update');
});

require __DIR__.'/settings.php';
