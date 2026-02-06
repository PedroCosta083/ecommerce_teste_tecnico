<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\Web\{ProductController, OrderController, CategoryController, TagController, RoleController, UserRoleController, PermissionController};

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
    
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class)->except(['create', 'store', 'destroy']);
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus')->middleware('permission:orders.update');
    Route::resource('categories', CategoryController::class);
    Route::resource('tags', TagController::class);
    
    Route::resource('roles', RoleController::class)->middleware('permission:roles.view');
    Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'destroy'])->middleware('permission:roles.view');
    Route::resource('users', UserRoleController::class)->only(['index'])->middleware('permission:users.view');
    Route::get('users/{user}/edit-roles', [UserRoleController::class, 'edit'])->name('users.edit-roles')->middleware('permission:roles.update');
    Route::put('users/{user}/roles', [UserRoleController::class, 'update'])->name('users.update-roles')->middleware('permission:roles.update');
});

require __DIR__.'/settings.php';
