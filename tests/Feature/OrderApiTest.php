<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Order, Product, Category};
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_can_list_orders()
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->withToken($this->token)->getJson('/api/v1/orders');

        $response->assertOk()->assertJsonStructure(['success', 'data']);
    }

    public function test_can_show_order()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withToken($this->token)->getJson("/api/v1/orders/{$order->id}");

        $response->assertOk()->assertJsonPath('data.id', $order->id);
    }

    public function test_can_create_order()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'quantity' => 100]);

        $response = $this->withToken($this->token)->postJson('/api/v1/orders', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ],
            'shipping_address' => 'Test Address',
            'billing_address' => 'Test Address'
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('orders', ['user_id' => $this->user->id]);
    }

    public function test_cannot_create_order_with_insufficient_stock()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'quantity' => 1]);

        $response = $this->withToken($this->token)->postJson('/api/v1/orders', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 10]
            ],
            'shipping_address' => 'Test Address',
            'billing_address' => 'Test Address'
        ]);

        $response->assertStatus(500); // Service throws exception
    }

    public function test_can_update_order_status()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withToken($this->token)->putJson("/api/v1/orders/{$order->id}/status", [
            'status' => 'processando'
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'processando']);
    }
}
