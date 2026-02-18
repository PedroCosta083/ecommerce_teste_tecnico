<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_can_list_products()
    {
        Product::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => ['id', 'name', 'slug', 'price']
                    ]
                ]
            ]);
    }

    public function test_can_create_product()
    {
        $category = Category::factory()->create();

        $data = [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test description',
            'price' => 99.99,
            'cost_price' => 50.00,
            'quantity' => 10,
            'min_quantity' => 2,
            'category_id' => $category->id,
            'active' => true
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/products', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Test Product');

        $this->assertDatabaseHas('products', ['slug' => 'test-product']);
    }

    public function test_can_show_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_can_update_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/v1/products/{$product->id}", [
                'name' => 'Updated Product',
                'price' => 199.99
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Product');
    }

    public function test_can_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
