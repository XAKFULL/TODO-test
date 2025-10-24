<?php

namespace App\Http\Controllers;

use App\DTO\TaskQueryParams;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Tasks
 *
 * Методы для управления тегами задач
 */
class TaskController extends Controller
{
    public function __construct(
        private readonly TaskRepository $taskRepository
    ) {}

    /**
     * Get all tasks with pagination, filtering and sorting
     *
     * @queryParam search string Search by title or description. Example: "important task"
     * @queryParam status string Filter by status. Options: pending, in_progress, completed
     * @queryParam tags array Filter by tags (array). Example: ["urgent", "important"]
     * @queryParam sort string Sort field. Default: created_at. Options: title, status, created_at, updated_at
     * @queryParam direction string Sort direction. Default: desc. Options: asc, desc
     * @queryParam per_page integer Number of items per page. Default: 15. Range: 1-100
     * @queryParam page integer Current page. Default: 1
     * @queryParam with_tags bool append tags. Default: false
     *
     **/
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $params = TaskQueryParams::fromRequest($request);
        $tasks = $this->taskRepository->paginate($params);

        return TaskResource::collection($tasks);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $tasks = $this->taskRepository->all();

        return TaskResource::collection($tasks);
    }

    public function store(TaskRequest $request): TaskResource
    {
        $task = $this->taskRepository->create($request->validated());

        $task->load('tags');

        return new TaskResource($task);
    }

    public function show(int $taskId): TaskResource
    {
        $task = $this->taskRepository->find($taskId);

        return new TaskResource($task);
    }

    public function update(TaskRequest $request, Task $task): TaskResource
    {
        $task = $this->taskRepository->update($task, $request->validated());

        return new TaskResource($task);
    }

    public function destroy(Task $task): \Illuminate\Http\Response
    {
        $this->taskRepository->delete($task);

        return response()->noContent();
    }
}
