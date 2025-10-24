<?php

namespace Tests\Feature;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_name_must_be_unique()
    {
        Tag::factory()->create(['name' => 'urgent']);

        $response = $this->postJson('/api/tags', ['name' => 'Urgent']); // Проверяем с другим регистром

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonPath('errors.name.0', 'Tag with this name already exists');
    }

    public function test_name_is_normalized()
    {
        $response = $this->postJson('/api/tags', ['name' => '  URGENT  ']);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'urgent');

        $this->assertDatabaseHas('tags', ['name' => 'urgent']);
    }

    public function test_name_cannot_exceed_50_characters()
    {
        $response = $this->postJson('/api/tags', [
            'name' => str_repeat('a', 51),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonPath('errors.name.0', 'Tag name cannot exceed 50 characters');
    }
}
