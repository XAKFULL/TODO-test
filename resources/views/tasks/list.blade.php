@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Список задач</h1>
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

    <!-- Фильтры -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Поиск -->
            <div>
                <label class="block text-sm font-medium mb-1">Поиск</label>
                <input type="text" id="search-input" class="w-full border rounded px-3 py-2" placeholder="Название, описание...">
            </div>
            <!-- Сортировка -->
            <div>
                <label class="block text-sm font-medium mb-1">Сортировка</label>
                <select id="sort-by" class="w-full border rounded px-3 py-2">
                    <option value="created_at|desc">Сначала новые</option>
                    <option value="created_at|asc">Сначала старые</option>
                    <option value="title|asc">По названию (А→Я)</option>
                    <option value="title|desc">По названию (Я→А)</option>
                    <option value="status|asc">По статусу (А→Я)</option>
                    <option value="status|desc">По статусу (Я→А)</option>
                </select>
            </div>
            <!-- Статус -->
            <div>
                <label class="block text-sm font-medium mb-1">Статус</label>
                <select id="status-filter" class="w-full border rounded px-3 py-2">
                    <option value="">Все</option>
                    <option value="pending">Ожидает</option>
                    <option value="in_progress">В работе</option>
                    <option value="completed">Завершено</option>
                </select>
            </div>

            <!-- Теги -->
            <div>
                <label class="block text-sm font-medium mb-1">Теги</label>
                <div class="relative">
                    <input type="text"
                           id="tag-input"
                           class="w-full border rounded px-3 py-2"
                           placeholder="Начните вводить тег...">
                    <div id="tag-suggestions" class="absolute z-10 bg-white border border-gray-300 rounded shadow-lg mt-1 w-full hidden"></div>
                </div>
                <div id="selected-tags" class="mt-2 flex flex-wrap gap-2"></div>
            </div>

            <!-- На страницу -->
            <div>
                <label class="block text-sm font-medium mb-1">На страницу</label>
                <select id="per-page" class="w-full border rounded px-3 py-2">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Таблица задач -->
    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">№</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Задача</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Теги</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
            </tr>
            </thead>
            <tbody id="tasks-table-body" class="bg-white divide-y divide-gray-200">
            <!-- Задачи подгрузятся сюда -->
            </tbody>
        </table>
    </div>

    <!-- Пагинация -->
    <div id="pagination" class="mt-4 flex justify-between items-center"></div>

    <!-- Модалки -->
    @include('partials.tasks.modal.create')
    @include('partials.tasks.modal.edit')
    @include('partials.tasks.modal.delete')
    @include('partials.tasks.modal.detail')
@endsection

@push('scripts')
    <script>
        // Подгрузим теги и задачи при загрузке
        document.addEventListener('DOMContentLoaded', () => {
            loadTags();
            loadTasks();
        });
    </script>
@endpush
