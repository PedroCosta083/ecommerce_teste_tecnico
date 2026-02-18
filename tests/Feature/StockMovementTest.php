<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Product, Category, StockMovement};
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockMovementTest extends TestCase
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

    public function test_can_list_stock_movements()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        StockMovement::factory()->count(3)->create(['product_id' => $product->id]);

        $response = $this->withToken($this->token)->getJson('/api/v1/stock-movements');

        $response->assertOk();
    }

    public function test_can_create_stock_movement()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'quantity' => 100]);

        $response = $this->withToken($this->token)->postJson('/api/v1/stock-movements', [
            'product_id' => $product->id,
            'type' => 'entrada',
            'quantity' => 50,
            'reason' => 'Reposição'
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('stock_movements', ['product_id' => $product->id, 'type' => 'entrada']);
    }

    public function test_stock_movement_updates_product_quantity()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'quantity' => 100]);

        $this->withToken($this->token)->postJson('/api/v1/stock-movements', [
            'product_id' => $product->id,
            'type' => 'entrada',
            'quantity' => 50
        ]);

        $product->refresh();
        $this->assertEquals(150, $product->quantity);
    }

    public function test_can_get_product_stock_summary()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        StockMovement::factory()->count(5)->create(['product_id' => $product->id]);

        $response = $this->withToken($this->token)->getJson("/api/v1/products/{$product->id}/stock-summary");

        $response->assertOk();
    }
}
