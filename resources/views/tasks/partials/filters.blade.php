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