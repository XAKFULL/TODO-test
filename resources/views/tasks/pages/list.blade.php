@extends('tasks.layouts.app')

@section('content')
    @include('tasks.partials.filters')
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

@endsection