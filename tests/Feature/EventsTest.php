<?php

namespace Tests\Feature;

use App\Events\OrderCreated;
use App\Events\ProductCreated;
use App\Events\StockLow;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class EventsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_product_created_event_is_dispatched(): void
    {
        Event::fake([ProductCreated::class]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create();

        $this->postJson('/api/v1/products', [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test description',
            'price' => 100.00,
            'cost_price' => 50.00,
            'quantity' => 10,
            'min_quantity' => 5,
            'category_id' => $category->id,
            'active' => true,
        ]);

        Event::assertDispatched(ProductCreated::class);
    }

    public function test_order_created_event_is_dispatched(): void
    {
        Event::fake([OrderCreated::class]);

        $user = User::factory()->create();
        $product = Product::factory()->create(['quantity' => 100]);

        $this->actingAs($user);

        $this->postJson('/api/v1/orders', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                ],
            ],
            'shipping_address' => '123 Main St',
            'billing_address' => '123 Main St',
        ]);

        Event::assertDispatched(OrderCreated::class);
    }

    public function test_stock_low_event_is_dispatched(): void
    {
        Event::fake([StockLow::class]);

        $product = Product::factory()->create([
            'quantity' => 10,
            'min_quantity' => 15,
        ]);

        $product->decrement('quantity', 5);
        $product->refresh();

        if ($product->quantity < $product->min_quantity) {
            StockLow::dispatch($product);
        }

        Event::assertDispatched(StockLow::class, function ($event) use ($product) {
            return $event->product->id === $product->id;
        });
    }
}
