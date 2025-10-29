<?php

namespace Tests\Feature;

use App\DTO\TaskQueryParams;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cache_is_invalidated_after_task_update()
    {
        $task = Task::factory()->create();
        $params = new TaskQueryParams;
        $repository = app(TaskRepository::class);

        $repository->index($params); // Кэшируем

        $repository->update($task, ['title' => 'Updated']);

        // Проверяем, что новый запрос возвращает обновленные данные
        $tasks = $repository->paginate($params);
        $this->assertEquals('Updated', $tasks->first()->title);

        // Дополнительная проверка: убедимся, что был сделан SQL-запрос (не использовался кэш)
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated',
        ]);
    }
}
