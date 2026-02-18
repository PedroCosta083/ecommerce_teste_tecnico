<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Product, Category};
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomValidationRulesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function has_stock_rule_validates_sufficient_stock()
    {
        $product = Product::factory()->create(['quantity' => 5]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/cart/items', [
                'product_id' => $product->id,
                'quantity' => 10
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);
    }

    /** @test */
    public function has_stock_rule_passes_with_sufficient_stock()
    {
        $product = Product::factory()->create(['quantity' => 10]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/cart/items', [
                'product_id' => $product->id,
                'quantity' => 5,
                'session_id' => 'test-session'
            ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function unique_slug_rule_validates_duplicate_slug_in_products()
    {
        Product::factory()->create(['slug' => 'test-product']);

        $category = Category::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/products', [
                'name' => 'Test Product 2',
                'slug' => 'test-product',
                'price' => 100,
                'cost_price' => 50,
                'quantity' => 10,
                'min_quantity' => 2,
                'category_id' => $category->id
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    /** @test */
    public function unique_slug_rule_passes_with_unique_slug()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/products', [
                'name' => 'Unique Product',
                'slug' => 'unique-product',
                'price' => 100,
                'cost_price' => 50,
                'quantity' => 10,
                'min_quantity' => 2,
                'category_id' => $category->id
            ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function unique_slug_rule_ignores_own_id_on_update()
    {
        $product = Product::factory()->create(['slug' => 'existing-slug']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/products/{$product->id}", [
                'slug' => 'existing-slug',
                'name' => 'Updated Name'
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function valid_parent_category_rule_validates_non_existent_parent()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/categories', [
                'name' => 'Test Category',
                'slug' => 'test-category',
                'parent_id' => 999
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['parent_id']);
    }

    /** @test */
    public function valid_parent_category_rule_prevents_self_reference()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/categories/{$category->id}", [
                'parent_id' => $category->id
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['parent_id']);
    }

    /** @test */
    public function valid_parent_category_rule_prevents_circular_reference()
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/categories/{$parent->id}", [
                'parent_id' => $child->id
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['parent_id']);
    }

    /** @test */
    public function valid_parent_category_rule_passes_with_valid_parent()
    {
        $parent = Category::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/categories', [
                'name' => 'Child Category',
                'slug' => 'child-category',
                'parent_id' => $parent->id
            ]);

        $response->assertStatus(201);
    }
}
