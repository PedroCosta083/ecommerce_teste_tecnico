<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals($category->id, $product->category->id);
    }

    public function test_product_has_many_tags(): void
    {
        $product = Product::factory()->create();
        $tags = Tag::factory()->count(3)->create();
        $product->tags()->attach($tags->pluck('id'));

        $this->assertCount(3, $product->tags);
        $this->assertInstanceOf(Tag::class, $product->tags->first());
    }

    public function test_active_scope_returns_only_active_products(): void
    {
        Product::factory()->create(['active' => true]);
        Product::factory()->create(['active' => false]);

        $activeProducts = Product::active()->get();

        $this->assertCount(1, $activeProducts);
        $this->assertTrue($activeProducts->first()->active);
    }

    public function test_in_stock_scope_returns_products_with_quantity(): void
    {
        Product::factory()->create(['quantity' => 10]);
        Product::factory()->create(['quantity' => 0]);

        $inStockProducts = Product::inStock()->get();

        $this->assertCount(1, $inStockProducts);
        $this->assertGreaterThan(0, $inStockProducts->first()->quantity);
    }

    public function test_low_stock_scope_returns_products_below_minimum(): void
    {
        Product::factory()->create(['quantity' => 5, 'min_quantity' => 10]);
        Product::factory()->create(['quantity' => 15, 'min_quantity' => 10]);

        $lowStockProducts = Product::lowStock()->get();

        $this->assertCount(1, $lowStockProducts);
    }

    public function test_product_uses_soft_deletes(): void
    {
        $product = Product::factory()->create();
        $product->delete();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
        $this->assertNotNull($product->fresh()->deleted_at);
    }
}
