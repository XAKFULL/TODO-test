<div id="editTaskModal" class="fixed inset-0 task-modal bg-opacity-100 hidden flex items-center justify-center">
    <div class="modal-content bg-white p-6 rounded shadow-lg w-96">
        <h3 class="text-lg font-semibold mb-4">Редактировать задачу</h3>
        <form id="editTaskForm">
            <input type="hidden" id="edit-task-id">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Название</label>
                <input type="text" id="edit-task-title" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Описание</label>
                <textarea id="edit-task-description" class="w-full border rounded px-3 py-2"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Статус</label>
                <select id="edit-task-status" class="w-full border rounded px-3 py-2">
                    <option value="pending">Ожидает</option>
                    <option value="in_progress">В Работе</option>
                    <option value="completed">Завершено</option>
                </select>
            </div>
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium">Теги</label>
                    <button type="button" id="add-tag-btn" class="text-blue-500 text-sm">+ Добавить тег</button>
                </div>
                <div id="edit-tags-container" class="space-y-2">
                    <!-- Теги будут добавляться сюда -->
                </div>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" class="px-4 py-2 text-gray-600 rounded hover:bg-gray-100"
                        onclick="closeModal('editTaskModal')">
                    Отмена
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Сохранить
                </button>
            </div>

        </form>
    </div>
</div>
