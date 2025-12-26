<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Contracts\TagRepositoryInterface;
use App\Repositories\Eloquent\TagRepository;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use App\Repositories\Eloquent\OrderItemRepository;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Eloquent\CartRepository;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use App\Repositories\Eloquent\StockMovementRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderItemRepositoryInterface::class, OrderItemRepository::class);
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
        $this->app->bind(StockMovementRepositoryInterface::class, StockMovementRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}