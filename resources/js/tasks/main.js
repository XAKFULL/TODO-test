import { TaskModal } from './components/taskModal.js';
import { ListFilters } from './components/listFilters.js';
import { Pagination } from './components/pagination.js';
import { taskAPI } from './api/tasks.js';
import { tagAPI } from './api/tags.js';

// Глобальные переменные
let listFilters;
let pagination;
let currentFilters = {
    search: '',
    status: '',
    tags: [],
    sort: 'created_at',
    direction: 'desc',
    page: 1,
    per_page: 15
};

// --- Загрузка задач ---
window.loadTasks = async () => {
    try {
        const { data, meta } = await taskAPI.paginate(currentFilters);
        const container = document.getElementById('tasks-table-body');
        const kanbanContainer = document.getElementById('kanban-table');

        if (!container) {
            if (!kanbanContainer) {
                console.error('Undefined page, cant load tasks');
                return;
            }
            return;
        }

        container.innerHTML = '';

        if (!data || data.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">
                        Нет задач по заданным критериям
                    </td>
                </tr>
            `;
            return;
        }
        data.forEach(task => {
            const row = document.createElement('tr');
            row.addEventListener('click', (e) => {
                if (!e.target.closest('.task-actions')) {
                    openModal('preview', task.id);
                }
            });
            row.className = 'border-b hover:bg-gray-50 task-row';
            row.dataset.id = task.id;
            row.innerHTML = `
                <td class="p-3">${task.id}</td>
                <td class="p-3">${task.title || '—'}</td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded text-xs ${
                task.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    task.status === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                        'bg-green-100 text-green-800'
            }">
                        ${task.status_label}
                    </span>
                </td>
                <td class="p-3">
                    ${task.tags?.map(tag => `
                        <span class="inline-block bg-gray-100 px-2 py-0.5 text-xs rounded mr-1">
                            ${tag.name}
                        </span>
                    `).join('') || '—'}
                </td>
                <td class="p-3 task-actions">
                    <button class="edit-btn text-blue-600 hover:text-blue-900 mr-2">✏️</button>
                    <button class="delete-btn text-red-600 hover:text-red-900">🗑️</button>
                </td>
            `;
            container.appendChild(row);
        });

        // Добавляем обработчики
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.closest('tr').dataset.id;
                taskModal.show('edit', id);
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.closest('tr').dataset.id;
                taskModal.show('delete', id);
            });
        });

        // Обновляем пагинацию
        pagination?.render(meta);
    } catch (err) {
        console.error('Error loading tasks:', err);
        const container = document.getElementById('tasks-table-body');
        if (container) {
            container.innerHTML = `
                <tr>
                    <td colspan="5" class="p-4 text-center text-red-500">
                        Ошибка загрузки: ${err.message}
                    </td>
                </tr>
            `;
        }
    }
};

// --- Загрузка тегов ---
const loadTags = async () => {
    try {
        const tags = await tagAPI.all();
        const container = document.getElementById('tags-container');
        if (container) {
            container.innerHTML = tags.map(tag => `
                <div class="tag-item" data-id="${tag.id}">
                    <span class="bg-gray-100 px-2 py-1 rounded">${tag.name}</span>
                    <button class="delete-tag text-red-500 ml-2">×</button>
                </div>
            `).join('');

            document.querySelectorAll('.delete-tag').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = e.target.closest('.tag-item').dataset.id;
                    tagAPI.delete(id).then(() => loadTags());
                });
            });
        }
    } catch (err) {
        console.error('Error loading tags:', err);
    }
};

// --- Инициализация ---
document.addEventListener('DOMContentLoaded', () => {
    // Инициализация компонентов
    listFilters = new ListFilters();
    pagination = new Pagination();
    window.taskModal = new TaskModal();

    window.openModal = (mode, id = null) => {
        window.taskModal.show(mode, id);
    };

    // Слушаем события
    window.addEventListener('filters-changed', (e) => {
        currentFilters = { ...currentFilters, ...e.detail };
        loadTasks();
    });

    window.addEventListener('pagination-changed', (e) => {
        currentFilters.page = e.detail.page;
        loadTasks();
    });

    window.addEventListener('task-deleted', () => {
        loadTasks();
    });

    window.addEventListener('task-deleted', () => {
        loadTasks();
    });

    // Загрузка данных
    loadTasks();
    loadTags();
});