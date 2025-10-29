// resources/js/kanban.js
import { taskAPI } from './../api/tasks.js';

export class KanbanBoard {
    constructor() {
        this.columns = ['pending', 'in_progress', 'completed'];
        this.initDragAndDrop();
    }

    initDragAndDrop() {
        // Инициализируем перетаскивание
        const taskCards = document.querySelectorAll('.task-card');
        taskCards.forEach(card => {
            card.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('task-id', card.dataset.taskId);
                card.classList.add('opacity-50');
            });

            card.addEventListener('dragend', () => {
                card.classList.remove('opacity-50');
            });
        });

        // Добавляем обработчики для колонок
        this.columns.forEach(column => {
            const columnEl = document.getElementById(`column-${column}`);
            if (!columnEl) return;

            columnEl.addEventListener('dragover', (e) => {
                e.preventDefault();
                columnEl.classList.add('border-blue-400');
            });

            columnEl.addEventListener('dragleave', () => {
                columnEl.classList.remove('border-blue-400');
            });

            columnEl.addEventListener('drop', async (e) => {
                e.preventDefault();
                columnEl.classList.remove('border-blue-400');

                const taskId = e.dataTransfer.getData('task-id');
                const newStatus = column;

                // ✅ ИСПОЛЬЗУЕМ СУЩЕСТВУЮЩИЕ МЕТОДЫ (БЕЗ ДОП. API)
                try {
                    // Получаем текущую задачу
                    const task = await taskAPI.get(taskId);

                    // Обновляем статус
                    task.status = newStatus;

                    // Сохраняем через существующий save() метод
                    await taskAPI.save(task);

                    // Перемещаем задачу в новую колонку
                    this.moveTaskToColumn(taskId, newStatus);
                    this.updateCounters();
                } catch (err) {
                    console.error('Error updating task status:', err);
                    alert('Не удалось обновить статус задачи');
                }
            });
        });
    }

    moveTaskToColumn(taskId, status) {
        const taskElement = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
        if (taskElement) {
            const column = document.getElementById(`column-${status}`);
            if (column) {
                column.appendChild(taskElement);
            }
        }
    }

    updateCounters() {
        this.columns.forEach(column => {
            const count = document.querySelectorAll(`#column-${column} .task-card`).length;
            const counterElement = document.getElementById(`count-${column}`);
            if (counterElement) counterElement.textContent = count;
        });
    }
}

// Инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('kanban-table')) {
        new KanbanBoard();
    }
});