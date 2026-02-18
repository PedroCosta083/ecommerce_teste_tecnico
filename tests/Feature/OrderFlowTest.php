<?php

namespace Tests\Feature;

use App\Jobs\ProcessOrder;
use App\Jobs\SendOrderConfirmation;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_complete_order_flow_with_jobs(): void
    {
        Queue::fake();
        Mail::fake();

        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'quantity' => 100,
            'price' => 50.00,
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/api/v1/orders', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                ],
            ],
            'shipping_address' => '123 Main St, City, State 12345',
            'billing_address' => '123 Main St, City, State 12345',
            'notes' => 'Test order',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'status',
                'total',
                'subtotal',
                'tax',
                'shipping_cost',
            ],
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        Queue::assertPushed(ProcessOrder::class);
        Queue::assertPushed(SendOrderConfirmation::class);
    }

    public function test_order_creation_validates_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['quantity' => 5]);

        $this->actingAs($user);

        $response = $this->postJson('/api/v1/orders', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                ],
            ],
            'shipping_address' => '123 Main St',
            'billing_address' => '123 Main St',
        ]);

        $response->assertStatus(500);
    }
}
