<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Events\ProductCreated;
use App\Events\ProductStockChanged;
use App\Events\StockLow;
use App\Listeners\CreateLowStockNotification;
use App\Listeners\LogLowStock;
use App\Listeners\LogProductCreation;
use App\Listeners\NotifyLowStock;
use App\Listeners\ProcessOrderCreated;
use App\Listeners\RecordStockMovement;
use App\Listeners\SendOrderCreatedNotification;
use App\Listeners\SendProductCreatedNotification;
use App\Models\Order;
use App\Models\Product;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\DashboardRepositoryInterface::class,
            \App\Repositories\DashboardRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Product::observe(ProductObserver::class);
        Order::observe(OrderObserver::class);

        // Rate Limiters
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many requests. Please try again later.',
                    ], 429, $headers);
                });
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many login attempts. Please try again later.',
                    ], 429, $headers);
                });
        });

        // Event Listeners
        Event::listen(ProductCreated::class, LogProductCreation::class);
        Event::listen(ProductCreated::class, SendProductCreatedNotification::class);

        Event::listen(OrderCreated::class, ProcessOrderCreated::class);
        Event::listen(OrderCreated::class, SendOrderCreatedNotification::class);

        Event::listen(StockLow::class, NotifyLowStock::class);
        Event::listen(StockLow::class, LogLowStock::class);
        Event::listen(StockLow::class, CreateLowStockNotification::class);
        
        Event::listen(ProductStockChanged::class, RecordStockMovement::class);
    }
}
