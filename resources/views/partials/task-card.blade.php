<div class="task-card card mb-3 shadow-sm border-primary" data-id="{{ $task->id }}">
    <div class="card-body">
        <h5 class="card-title mb-1">{{ $task->title }}</h5>
        <p class="text-muted mb-3">{{ $task->description }}</p>

        <div class="mb-3 task-tags">
            @foreach($task->tags as $tag)
                <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
            @endforeach
        </div>

        <div class="d-flex justify-content-between">
            <small class="text-muted">
                Created: {{ $task->created_at->format('M d, Y') }}
            </small>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary btn-edit me-1">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger btn-delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>
