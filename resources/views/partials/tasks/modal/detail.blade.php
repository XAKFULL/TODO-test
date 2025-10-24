<div id="taskDetailModal" class="task-modal fixed inset-0 hidden flex items-center justify-center z-50">
    <div class="task-modal-content bg-white rounded shadow-lg p-6 w-full max-w-lg mx-4">
        <div class="flex justify-between items-start mb-4">
            <h3 id="detail-task-title" class="text-xl font-bold"></h3>
            <button type="button" onclick="closeModal('taskDetailModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>

        <p id="detail-task-description" class="text-gray-700 mb-4"></p>

        <div class="mb-4">
            <span class="font-medium">Статус:</span>
            <span id="detail-task-status" class="ml-2 capitalize"></span>
        </div>

        <div class="mb-4">
            <span class="font-medium">Теги:</span>
            <div id="detail-task-tags" class="mt-1 flex flex-wrap gap-1"></div>
        </div>

        <div class="flex justify-end space-x-2">
            <button type="button" class="px-4 py-2 text-gray-600 rounded hover:bg-gray-100"
                    onclick="closeModal('taskDetailModal')">
                Закрыть
            </button>
            <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    onclick="openEditModal(document.getElementById('detail-task-id').value)">
                Редактировать
            </button>
        </div>

        <input type="hidden" id="detail-task-id">
    </div>
</div>
