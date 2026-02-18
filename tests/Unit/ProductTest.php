<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_category()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals($category->id, $product->category->id);
    }

    public function test_product_has_many_tags()
    {
        $product = Product::factory()->create();
        $tags = Tag::factory()->count(3)->create();
        $product->tags()->attach($tags->pluck('id'));

        $this->assertCount(3, $product->tags);
    }

    public function test_active_scope_filters_active_products()
    {
        Product::factory()->create(['active' => true]);
        Product::factory()->create(['active' => false]);

        $activeProducts = Product::active()->get();

        $this->assertCount(1, $activeProducts);
        $this->assertTrue($activeProducts->first()->active);
    }

    public function test_in_stock_scope_filters_products_with_stock()
    {
        Product::factory()->create(['quantity' => 10]);
        Product::factory()->create(['quantity' => 0]);

        $inStockProducts = Product::inStock()->get();

        $this->assertCount(1, $inStockProducts);
        $this->assertGreaterThan(0, $inStockProducts->first()->quantity);
    }

    public function test_low_stock_scope_filters_low_stock_products()
    {
        Product::factory()->create(['quantity' => 2, 'min_quantity' => 5]);
        Product::factory()->create(['quantity' => 10, 'min_quantity' => 5]);

        $lowStockProducts = Product::lowStock()->get();

        $this->assertCount(1, $lowStockProducts);
    }
}
