<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_add_product_to_cart()
    {
        $product = Product::factory()->create(['quantity' => 10]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/cart/items', [
                'product_id' => $product->id,
                'quantity' => 2,
                'user_id' => $this->user->id
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }

    public function test_can_view_cart()
    {
        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create();
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/cart?user_id={$this->user->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    public function test_can_update_cart_item_quantity()
    {
        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create(['quantity' => 10]);
        $item = $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/cart/items/{$item->id}", [
                'quantity' => 3
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'quantity' => 3
        ]);
    }

    public function test_can_remove_item_from_cart()
    {
        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create();
        $item = $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/cart/items/{$item->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }
}
