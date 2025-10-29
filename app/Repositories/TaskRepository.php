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
    private const string CACHE_KEY = 'tasks';

    private const string CACHE_VERSION_KEY = 'tasks:version';

    private const int CACHE_TTL = 300;

    public function __construct(
        private readonly CacheRepository $cache
    ) {}

    public function index(TaskQueryParams $params): LengthAwarePaginator
    {
        return $this->paginate($params);
    }

    public function all(): Collection
    {
        return $this->cache->remember(
            self::CACHE_KEY.':'.$this->getCacheVersion(),
            self::CACHE_TTL,
            function () {
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

        $this->incrementCacheVersion();

        return $task;
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        $this->checkTags($task, $data);

        $this->incrementCacheVersion();

        return $task;
    }

    public function delete(Task $task): bool
    {
        $result = $task->delete();

        $this->incrementCacheVersion();

        return $result;
    }

    private function getCacheVersion(): int
    {
        return $this->cache->get(self::CACHE_VERSION_KEY, 0);
    }

    private function incrementCacheVersion(): void
    {
        $this->cache->increment(self::CACHE_VERSION_KEY);
    }

    public function paginate(TaskQueryParams $params): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey($params);

        return $this->cache->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn () => (new TaskQuery($params))->apply()->paginate(
                $params->perPage,
                ['*'],
                'page',
                $params->page
            )
        );
    }

    private function generateCacheKey(TaskQueryParams $params): string
    {
        // Включаем версию кэша в ключ
        return self::CACHE_KEY.':params:'.$this->getCacheVersion().':'.md5(json_encode($params->toArray()));
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
