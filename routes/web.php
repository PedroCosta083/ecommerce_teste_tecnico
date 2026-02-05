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
    
    Route::resource('products', ProductController::class)->middleware('permission:view products');
    Route::resource('orders', OrderController::class)->except(['create', 'store', 'destroy'])->middleware('permission:view orders');
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus')->middleware('permission:edit orders');
    Route::resource('categories', CategoryController::class)->middleware('permission:view categories');
    Route::resource('tags', TagController::class)->middleware('permission:view tags');
    
    // Roles e Permissions
    Route::resource('roles', RoleController::class)->middleware('permission:manage roles');
    Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'destroy'])->middleware('permission:manage permissions');
    Route::resource('users', UserRoleController::class)->only(['index'])->middleware('permission:view users');
    Route::get('users/{user}/edit-roles', [UserRoleController::class, 'edit'])->name('users.edit-roles')->middleware('permission:manage roles');
    Route::put('users/{user}/roles', [UserRoleController::class, 'update'])->name('users.update-roles')->middleware('permission:manage roles');
});

require __DIR__.'/settings.php';
