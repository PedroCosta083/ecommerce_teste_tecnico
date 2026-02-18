<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\{Category, Product};
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_products_relationship()
    {
        $category = Category::factory()->hasProducts(3)->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $category->products);
        $this->assertCount(3, $category->products);
    }

    public function test_category_has_parent_relationship()
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $this->assertEquals($parent->id, $child->parent->id);
    }

    public function test_category_has_children_relationship()
    {
        $parent = Category::factory()->create();
        Category::factory()->count(2)->create(['parent_id' => $parent->id]);

        $this->assertCount(2, $parent->children);
    }

    public function test_category_slug_is_generated()
    {
        $category = Category::factory()->create(['name' => 'Test Category']);

        $this->assertNotNull($category->slug);
    }
}
