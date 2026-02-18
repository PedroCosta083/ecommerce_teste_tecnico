<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Category};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidationTest extends TestCase
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

    public function test_product_requires_name()
    {
        $category = Category::factory()->create();

        $response = $this->withToken($this->token)->postJson('/api/v1/products', [
            'price' => 100,
            'quantity' => 10,
            'category_id' => $category->id
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_product_requires_positive_price()
    {
        $category = Category::factory()->create();

        $response = $this->withToken($this->token)->postJson('/api/v1/products', [
            'name' => 'Test',
            'slug' => 'test',
            'price' => -10,
            'quantity' => 10,
            'category_id' => $category->id
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }

    public function test_product_requires_valid_category()
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/products', [
            'name' => 'Test',
            'slug' => 'test',
            'price' => 100,
            'quantity' => 10,
            'category_id' => 999
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }

    public function test_order_requires_items()
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/orders', [
            'shipping_address' => 'Test',
            'billing_address' => 'Test'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    public function test_order_requires_addresses()
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/orders', [
            'items' => [['product_id' => 1, 'quantity' => 1]]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['shipping_address', 'billing_address']);
    }

    public function test_cart_requires_product_id()
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/cart/items', [
            'quantity' => 2
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);
    }

    public function test_cart_requires_positive_quantity()
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/cart/items', [
            'product_id' => 1,
            'quantity' => 0
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }
}
