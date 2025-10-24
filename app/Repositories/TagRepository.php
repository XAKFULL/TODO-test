<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Models\Task;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Collection;

// TODO можно сделать интерфейс с разделением (запись, чтение, пагинация но избыточно в данном случае)
class TagRepository
{
    private const CACHE_KEY = 'tags';

    private const CACHE_TTL = 300;

    public function __construct(
        private readonly CacheRepository $cache
    ) {}

    public function all(): Collection
    {
        return $this->cache->remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Tag::all();
        });
    }

    public function find(int $id): ?Tag
    {
        return Tag::find($id);
    }

    public function create(array $data): Tag
    {
        $task = Tag::create($data);

        $this->clearCache();

        return $task;
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        $this->clearCache();

        return $task;
    }

    public function delete(Tag $task): bool
    {
        $result = $task->delete();

        $this->clearCache();

        return $result;
    }

    private function clearCache(): void
    {
        $this->cache->forget(self::CACHE_KEY);
    }
}
