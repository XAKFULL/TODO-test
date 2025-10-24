<div id="deleteTaskModal" class="task-modal fixed inset-0 hidden flex items-center justify-center z-50">
    <div class="task-modal-content bg-white rounded shadow-lg p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-lg font-semibold">Удалить задачу?</h3>
            <button type="button" onclick="closeModal('deleteTaskModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>

        <p id="delete-task-title" class="mb-6">Вы уверены, что хотите удалить задачу?</p>

        <div class="flex justify-end space-x-2">
            <button type="button" class="px-4 py-2 text-gray-600 rounded hover:bg-gray-100"
                    onclick="closeModal('deleteTaskModal')">
                Отмена
            </button>
            <button type="button" id="confirm-delete-btn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                Удалить
            </button>
        </div>
    </div>
</div>
