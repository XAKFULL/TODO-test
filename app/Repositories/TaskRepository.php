<?php

namespace App\Repositories;

use App\DTO\TaskQueryParams;
use App\Models\Tag;
use App\Models\Task;
use App\Queries\TaskQuery;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

// TODO можно сделать интерфейс с разделением (запись, чтение, пагинация но избыточно в данном случае)
class TaskRepository
{
    private const CACHE_KEY = 'tasks';

    private const CACHE_TTL = 300;

    public function __construct(
        private readonly CacheRepository $cache
    ) {}

    public function index(TaskQueryParams $params)
    {
        return $this->paginate($params);
    }

    public function all(): Collection
    {
        return $this->cache->remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Task::all();
        });
    }

    public function find(int $id): ?Task
    {
        return Task::with('tags')->find($id);
    }

    public function create(array $data): Task
    {
        $task = Task::create($data);

        $this->checkTags($task, $data);

        $this->clearCache();

        return $task;
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        $this->checkTags($task, $data);

        $this->clearCache();

        return $task;
    }

    public function delete(Task $task): bool
    {
        $result = $task->delete();

        $this->clearCache();

        return $result;
    }

    private function clearCache(): void
    {
        $this->cache->forget(self::CACHE_KEY);
    }

    public function paginate(TaskQueryParams $params): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey($params);

        return $this->cache->remember($cacheKey, self::CACHE_TTL, function () use ($params) {
            $query = (new TaskQuery($params))->apply();

            return $query->paginate(
                $params->perPage,
                ['*'],
                'page',
                $params->page
            );
        });
    }

    private function generateCacheKey(TaskQueryParams $params): string
    {
        return self::CACHE_KEY.':params:'.md5(json_encode($params->toArray()));
    }

    private function checkTags(Task $task, array $data): void
    {
        if (array_key_exists('tags', $data)) {
            $this->attachTags($task, $data['tags']);
        }
    }

    private function attachTags(Task $task, array $tags): void
    {
        $tagIds = Tag::findOrCreate($tags);
        $task->tags()->sync($tagIds);
    }
}
