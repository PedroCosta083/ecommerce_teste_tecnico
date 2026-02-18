<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Tag};
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagApiTest extends TestCase
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

    public function test_can_list_tags()
    {
        Tag::factory()->count(5)->create();

        $response = $this->withToken($this->token)->getJson('/api/v1/tags');

        $response->assertOk()->assertJsonStructure(['success', 'data']);
    }

    public function test_can_show_tag()
    {
        $tag = Tag::factory()->create();

        $response = $this->withToken($this->token)->getJson("/api/v1/tags/{$tag->id}");

        $response->assertOk()->assertJsonPath('data.id', $tag->id);
    }

    public function test_can_create_tag()
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/tags', [
            'name' => 'New Tag',
            'slug' => 'new-tag',
            'color' => '#FF0000'
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('tags', ['name' => 'New Tag']);
    }

    public function test_can_update_tag()
    {
        $tag = Tag::factory()->create();

        $response = $this->withToken($this->token)->putJson("/api/v1/tags/{$tag->id}", [
            'name' => 'Updated Tag'
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'Updated Tag']);
    }

    public function test_can_delete_tag()
    {
        $tag = Tag::factory()->create();

        $response = $this->withToken($this->token)->deleteJson("/api/v1/tags/{$tag->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
