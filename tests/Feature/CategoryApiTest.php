<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Category};
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        $this->token = $this->admin->createToken('test')->plainTextToken;
    }

    public function test_can_list_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->withToken($this->token)->getJson('/api/v1/categories');

        $response->assertOk()
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_show_category()
    {
        $category = Category::factory()->create();

        $response = $this->withToken($this->token)->getJson("/api/v1/categories/{$category->id}");

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('data.id', $category->id);
    }

    public function test_can_create_category()
    {
        $data = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description'
        ];

        $response = $this->withToken($this->token)->postJson('/api/v1/categories', $data);

        $response->assertCreated()
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);
    }

    public function test_can_update_category()
    {
        $category = Category::factory()->create();

        $response = $this->withToken($this->token)->putJson("/api/v1/categories/{$category->id}", [
            'name' => 'Updated Name'
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Updated Name']);
    }

    public function test_can_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->withToken($this->token)->deleteJson("/api/v1/categories/{$category->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_can_list_category_products()
    {
        $category = Category::factory()->hasProducts(3)->create();

        $response = $this->withToken($this->token)->getJson("/api/v1/categories/{$category->id}/products");

        $response->assertOk()
            ->assertJsonStructure(['success', 'data']);
    }
}
