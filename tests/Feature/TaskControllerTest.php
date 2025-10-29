<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_tasks()
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'status', 'created_at', 'updated_at'],
                ],
            ]);
    }

    public function test_can_create_task()
    {
        $data = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'title', 'description', 'status', 'created_at', 'updated_at']]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'status' => 'pending', // в БД — status
        ]);
    }

    public function test_validation_fails_when_title_is_missing()
    {
        $data = [
            'description' => 'Test Description',
            'status' => 'pending',
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_can_delete_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    // === НОВЫЕ ТЕСТЫ ДЛЯ /api/tasks-paginated ===

    public function test_can_paginate_tasks()
    {
        Task::factory()->count(15)->create();

        $response = $this->postJson('/api/tasks-paginated', [
            'per_page' => 10,
            'page' => 1,
            'with_tags' => true,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'status', 'tags'],
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 15)
            ->assertJsonCount(10, 'data');
    }

    public function test_can_sort_tasks_by_title_asc()
    {
        Task::factory()->create(['title' => 'Z Task']);
        Task::factory()->create(['title' => 'A Task']);

        $response = $this->postJson('/api/tasks-paginated', [
            'sort' => 'title',
            'direction' => 'asc',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.0.title', 'A Task')
            ->assertJsonPath('data.1.title', 'Z Task');
    }

    public function test_can_sort_tasks_by_created_at_desc_by_default()
    {
        $oldTask = Task::factory()->create(['created_at' => now()->subDay()]);
        $newTask = Task::factory()->create(['created_at' => now()]);

        $response = $this->postJson('/api/tasks-paginated');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.id', $newTask->id) // новее — первая
            ->assertJsonPath('data.1.id', $oldTask->id);
    }

    public function test_invalid_sort_field_falls_back_to_default()
    {
        $oldTask = Task::factory()->create(['created_at' => now()->subDay()]);
        $newTask = Task::factory()->create(['created_at' => now()]);

        $response = $this->postJson('/api/tasks-paginated', [
            'sort_by' => 'invalid_field',
            'sort_dir' => 'asc',
        ]);

        // Должна примениться сортировка по умолчанию (created_at|desc)
        $response->assertStatus(200)
            ->assertJsonPath('data.0.id', $newTask->id);
    }

    public function test_can_filter_tasks_by_status()
    {
        Task::factory()->create(['status' => 'pending']);
        Task::factory()->create(['status' => 'completed']);

        $response = $this->postJson('/api/tasks-paginated', [
            'status' => 'pending',
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'pending');
    }

    public function test_can_create_task_with_tags()
    {
        $data = [
            'title' => 'Task with tags',
            'description' => 'Description',
            'status' => 'pending',
            'tags' => ['urgent', 'important'],
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201)

            ->assertJsonStructure([
                'data' => ['id', 'title', 'description', 'status', 'tags'],
            ])
            ->assertJsonCount(2, 'data.tags');

        $this->assertCount(2, Task::first()->tags);
        $this->assertDatabaseHas('tags', ['name' => 'urgent']);
        $this->assertDatabaseHas('tags', ['name' => 'important']);
    }

    public function test_can_update_task_with_tags()
    {
        $task = Task::factory()->create();
        $tag = Tag::factory()->create(['name' => 'old']);

        $task->tags()->attach($tag);

        $updateData = [
            'title' => 'Updated Task',
            'status' => 'in_progress',
            'tags' => ['new-tag', 'another'], // заменит старые теги
        ];

        $this->putJson("/api/tasks/{$task->id}", $updateData);
        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Task')
            ->assertJsonPath('data.status', 'in_progress')
            ->assertJsonCount(2, 'data.tags');

        $this->assertCount(2, $task->fresh()->tags);
        $this->assertDatabaseHas('tags', ['name' => 'new-tag']);
        $this->assertDatabaseHas('tags', ['name' => 'another']);
        $this->assertDatabaseMissing('tag_task', ['tag_id' => $tag->id]); // старый тег отвязан
    }

    public function test_can_filter_tasks_by_tags()
    {
        $urgentTag = Tag::firstOrCreate(['name' => 'urgent']);
        $bugTag = Tag::firstOrCreate(['name' => 'bug']);

        $task1 = Task::factory()->create();
        $task1->tags()->attach($urgentTag);

        $task2 = Task::factory()->create();
        $task2->tags()->attach($bugTag);

        Task::factory()->create(); // без тегов

        $response = $this->postJson('/api/tasks-paginated', [
            'tags' => ['urgent'],
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $task1->id);
    }

    public function test_can_filter_by_multiple_tags()
    {
        $urgent = Tag::firstOrCreate(['name' => 'urgent']);
        $bug = Tag::firstOrCreate(['name' => 'bug']);

        $task1 = Task::factory()->create();
        $task1->tags()->attach([$urgent->id, $bug->id]);

        $task2 = Task::factory()->create();
        $task2->tags()->attach($urgent->id);

        $response = $this->postJson('/api/tasks-paginated', [
            'tags' => ['bug'],
        ]);

        // Должна вернуться только задача с ОБОИМИ тегами
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $task1->id);
    }

    public function test_search_in_title_and_description()
    {
        Task::factory()->create(['title' => 'Login bug', 'description' => 'User cannot log in']);
        Task::factory()->create(['title' => 'Payment issue', 'description' => '']);

        $response = $this->postJson('/api/tasks-paginated', [
            'search' => 'bug',
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Login bug');
    }
}
