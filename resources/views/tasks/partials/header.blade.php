<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">{{ $pageTitle }}</h1>
    <div class="flex space-x-3">
        <a href="{{ route('home') }}"
           class="{{ request()->routeIs('home') ? 'hidden' : 'px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300' }}">
            Главная
        </a>
        <a href="{{ route('tasks.kanban') }}"
           class="{{ request()->routeIs('tasks.kanban') ? 'hidden' : 'px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300' }}">
            Канбан
        </a>
        <a href="{{ route('tasks.list') }}"
           class="{{ request()->routeIs('tasks.list') ? 'hidden' : 'px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300' }}">
            Список
        </a>
        <button type="button" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                onclick="openModal('create')">
            + Новая задача
        </button>
    </div>
</div>