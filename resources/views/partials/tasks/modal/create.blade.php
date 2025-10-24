<div id="createTaskModal" class="fixed task-modal inset-0 bg-opacity-100 hidden flex items-center justify-center z-50">
    <div class="modal-content bg-white rounded shadow-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold mb-4">Новая задача</h3>
        <form id="createTaskForm">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Название</label>
                <input type="text" id="create-task-title" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Описание</label>
                <textarea id="create-task-description" class="w-full border rounded px-3 py-2"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Статус</label>
                <select id="create-task-status" class="w-full border rounded px-3 py-2">
                    <option value="pending">Ожидает</option>
                    <option value="in_progress">В Работе</option>
                    <option value="completed">Завершено</option>
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded"
                        onclick="closeModal('createTaskModal')">
                    Отмена
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Создать
                </button>
            </div>
        </form>
    </div>
</div>
