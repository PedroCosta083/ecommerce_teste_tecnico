<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\{Product, Category};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelScopesTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_scope_returns_only_active_products()
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id, 'active' => true]);
        Product::factory()->count(2)->create(['category_id' => $category->id, 'active' => false]);

        $activeProducts = Product::active()->get();

        $this->assertCount(3, $activeProducts);
        $this->assertTrue($activeProducts->every(fn($p) => $p->active === true));
    }

    public function test_in_stock_scope_returns_products_with_quantity()
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id, 'quantity' => 10]);
        Product::factory()->count(2)->create(['category_id' => $category->id, 'quantity' => 0]);

        $inStock = Product::inStock()->get();

        $this->assertCount(3, $inStock);
        $this->assertTrue($inStock->every(fn($p) => $p->quantity > 0));
    }

    public function test_low_stock_scope_returns_products_below_minimum()
    {
        $category = Category::factory()->create();
        Product::factory()->count(2)->create([
            'category_id' => $category->id,
            'quantity' => 5,
            'min_quantity' => 10
        ]);
        Product::factory()->count(3)->create([
            'category_id' => $category->id,
            'quantity' => 20,
            'min_quantity' => 10
        ]);

        $lowStock = Product::lowStock()->get();

        $this->assertCount(2, $lowStock);
        $this->assertTrue($lowStock->every(fn($p) => $p->quantity < $p->min_quantity));
    }

    public function test_scopes_can_be_chained()
    {
        $category = Category::factory()->create();
        Product::factory()->create([
            'category_id' => $category->id,
            'active' => true,
            'quantity' => 5,
            'min_quantity' => 10
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'active' => false,
            'quantity' => 5,
            'min_quantity' => 10
        ]);

        $result = Product::active()->lowStock()->get();

        $this->assertCount(1, $result);
    }
}
