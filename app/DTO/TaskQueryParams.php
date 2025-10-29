<?php

namespace App\DTO;

use App\Enums\TaskStatusEnum;
use Illuminate\Http\Request;

readonly class TaskQueryParams
{
    public function __construct(
        public ?string $search = null,
        public ?TaskStatusEnum $status = null,
        public ?array $tags = null,
        public string $sort = 'created_at',
        public string $direction = 'desc',
        public int $page = 1,
        public int $perPage = 15,
        public bool $withTags = false
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->input('search'),
            status: TaskStatusEnum::tryFrom($request->input('status')),
            tags: is_array($request->input('tags')) ? $request->input('tags') : null,
            sort: $request->input('sort', 'created_at'),
            direction: $request->input('direction', 'desc'),
            page: max(1, (int) $request->input('page', 1)),
            perPage: max(1, (int) $request->input('per_page', 15)),
            withTags: max(false, (bool) $request->input('with_tags', false)),
        );
    }

    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'status' => $this->status,
            'tags' => $this->tags,
            'sort' => $this->sort,
            'direction' => $this->direction,
            'page' => $this->page,
            'per_page' => $this->perPage,
            'with_tags' => $this->withTags,
        ];
    }
}
