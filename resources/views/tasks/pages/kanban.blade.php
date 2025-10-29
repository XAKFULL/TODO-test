@extends('tasks.layouts.app')

@section('content')

    <div class="container mx-auto px-4 py-8" id="kanban-table">
        <!-- Канбан-доска -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach(['pending' => 'В ожидании', 'in_progress' => 'В работе', 'completed' => 'Завершено'] as $key => $status)
                <div class="bg-white p-4 rounded-lg shadow" id="column-body-{{ $key }}">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-semibold text-lg flex items-center">
                            @if($key === 'pending')
                                <span class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></span>
                            @elseif($key === 'in_progress')
                                <span class="w-3 h-3 bg-blue-400 rounded-full mr-2"></span>
                            @else
                                <span class="w-3 h-3 bg-green-400 rounded-full mr-2"></span>
                            @endif
                            {{ $status }}
                        </h2>
                        <span class="text-sm text-gray-500" id="count-{{ $key }}">{{$tasks->where('status', $key)->count()}}</span>
                    </div>

                    <div class="space-y-3 min-h-[100px]" id="column-{{ $key }}">
                        @foreach($tasks->where('status', $key) as $task)
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm task-card relative"
                                     data-task-id="{{ $task->id }}"
                                     data-status="{{ $task->status }}"
                                     draggable="true"
                                     onclick="openModal('preview', {{ $task->id }})"
                                >
                                    <h4 class="font-medium text-gray-900">{{ $task->id }}</h4>
                                    <p class="text-gray-600 text-sm mt-1 line-clamp-2">{{ $task->title }}</p>

                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach($task->tags as $tag)
                                            <span class="inline-block bg-gray-100 px-2 py-1 rounded text-xs">
                                            {{ $tag->name }}
                                        </span>
                                        @endforeach
                                    </div>

                                    <div class="mt-3 flex justify-between items-center">
                                        <div class="flex space-x-1">
                                            <button type="button"
                                                    class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded border border-blue-200"
                                                    onclick="openModal('edit', {{ $task->id }})">
                                                Редактировать
                                            </button>
                                            <button type="button"
                                                    class="text-red-600 hover:text-red-800 text-xs px-2 py-1 rounded border border-red-200"
                                                    onclick="openModal('delete', {{ $task->id }})">
                                                Удалить
                                            </button>
                                        </div>
                                        <span class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($task->created_at)->format('d.m.Y') }}
                                    </span>
                                    </div>
                                </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    @vite([ 'resources/js/tasks/components/kanban.js' ])

@endsection