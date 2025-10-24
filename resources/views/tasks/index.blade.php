@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Канбан</h1>
        <div class="flex space-x-3">
            <a href="{{ route('tasks.kanban') }}"
               class="{{ request()->routeIs('tasks.kanban') ? 'hidden' : 'px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300' }}">
                Канбан
            </a>
            <a href="{{ route('tasks.list') }}"
               class="{{ request()->routeIs('tasks.list') ? 'hidden' : 'px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300' }}">
                Список
            </a>
            <button type="button" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                    onclick="openModal('createTaskModal')">
                + Новая задача
            </button>
        </div>
    </div>

    <!-- Колонки канбан-доски -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach(['pending' => 'Ожидает', 'in_progress' => 'В работе', 'completed' => 'Завершено'] as $key => $status)
            <div class="bg-white p-4 rounded shadow" id="column-body-{{ $key }}">
                <h2 class="font-semibold mb-4 capitalize">{{ str_replace('_', ' ', $status) }}</h2>
                <div class="space-y-3 min-h-[100px]" id="column-{{ $key }}">
                    @foreach($tasks as $task)
                        @if($task->status === $key)
                            <div class="bg-gray-50 p-3 rounded border border-gray-200 task-card relative"
                                 data-task-id="{{ $task->id }}"
                                 data-status="{{ $task->status }}"
                                 draggable="true">
                                <h5 class="">№ {{ $task->id }}</h5>
                                <h3 class="font-medium">{{ $task->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $task->description }}</p>

                                <!-- Контейнер для тегов при наведении -->
                                <div class="task-tags-tooltip hidden absolute bottom-full left-0 mb-2 bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap z-10">
                                    Загрузка...
                                </div>

                                <div class="mt-2 flex space-x-2">
                                    <button type="button" class="text-blue-500 text-sm card-button"
                                            onclick="openEditModal({{ $task->id }})">
                                        Редактировать
                                    </button>
                                    <button type="button" class="text-red-500 text-sm"
                                            onclick="openDeleteModal({{ $task->id }}, '{{ addslashes($task->title) }}')">
                                        Удалить
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- Модалки -->
    @include('partials.tasks.modal.create')
    @include('partials.tasks.modal.edit')
    @include('partials.tasks.modal.detail')
    @include('partials.tasks.modal.delete')
@endsection
