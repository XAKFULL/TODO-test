<?php

namespace App\Queries;

use App\DTO\TaskQueryParams;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

class TaskQuery
{
    private const DEFAULT_SORT_FIELD = 'created_at';

    private const DEFAULT_SORT_DIRECTION = 'desc';

    private const ALLOWED_SORT_FIELDS = [
        'title', 'status', 'created_at', 'updated_at',
    ];

    public function __construct(private readonly TaskQueryParams $params) {}

    public function apply(): Builder
    {
        $query = Task::query();

        $this->applySearch($query);
        $this->applyStatusFilter($query);
        $this->applySorting($query);
        $this->appendTags($query);
        $this->applyTagFilter($query);

        return $query;
    }

    private function applySearch(Builder $query): void
    {
        if ($this->params->search) {
            $search = '%'.$this->params->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }
    }

    private function applyStatusFilter(Builder $query): void
    {
        if ($this->params->status) {
            $query->where('status', $this->params->status);
        }
    }

    private function applySorting(Builder $query): void
    {
        $sortField = in_array($this->params->sort, self::ALLOWED_SORT_FIELDS)
            ? $this->params->sort
            : self::DEFAULT_SORT_FIELD;

        $sortDirection = strtolower($this->params->direction) === self::DEFAULT_SORT_DIRECTION
            ? self::DEFAULT_SORT_DIRECTION
            : 'asc';

        $query->orderBy($sortField, $sortDirection);
    }

    private function applyTagFilter(Builder $query): void
    {
        if ($this->params->tags) {
            $query->whereHas('tags', function ($query) {
                $query->whereIn('name', $this->params->tags);
            });
        }
    }

    private function appendTags(Builder $query): void
    {
        if ($this->params->withTags) {
            $query->with('tags');
        }
    }
}
