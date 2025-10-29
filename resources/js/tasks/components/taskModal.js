import {taskAPI} from "../api/tasks.js";
import {Task} from "../models/Task.js";
import {TaskForm} from "./taskForm.js";

export class TaskModal {
    constructor() {
        this.element = document.getElementById('taskModal');
        this.body = document.getElementById('taskModalBody');
        this.closeBtn = this.element.querySelector('.close');
        this.closeBtn.onclick = () => this.hide();
        window.onclick = (e) => {
            if (e.target === this.element) this.hide();
        };
    }

    async show(mode, taskId = null) {
        if (mode === 'create') {
            this.renderForm(new Task({}));
            this.element.classList.remove('hidden');
            return;
        }

        if (!taskId) {
            alert('Task ID is required for edit/delete');
            return;
        }

        try {
            const task = await taskAPI.get(taskId);
            if (mode === 'edit') {
                this.renderForm(task);
            } else if (mode === 'delete') {
                // ИСПРАВЛЕНО: отображение имени задачи
                this.renderDelete(task);
            } else if (mode === 'preview') {
                this.renderPreview(task);
            }
        } catch (err) {
            alert('Failed to load task: ' + err.message);
        }

        this.element.classList.remove('hidden');
    }

    hide() {
        this.element.classList.add('hidden');
        this.body.innerHTML = '';
    }

    renderForm(task) {
        this.body.innerHTML = TaskForm.render(task);
        TaskForm.bind(task, (updatedTask) => {
            taskAPI.save(updatedTask).then(() => {
                this.hide();
                window.dispatchEvent(new CustomEvent('task-updated'));
            }).catch(err => alert('Save failed: ' + err.message));
        });
    }

    renderPreview(task) {
        this.body.innerHTML = `
          <h2 class="text-xl font-semibold mb-2">${task.title}</h2>
          <p class="text-gray-700 mb-4">${task.description || '—'}</p>
          <div class="mb-4">
            <strong>Статус:</strong> 
            <span class="px-2 py-1 rounded ${
            task.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                task.status === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                    'bg-green-100 text-green-800'
        }">${task.status_label}</span>
          </div>
          <div class="mb-4">
            <strong>Теги:</strong> 
            ${task.tags?.map(tag => `
                <span class="inline-block bg-gray-100 px-2 py-1 rounded mr-1">
                    ${tag.name}
                </span>
            `).join('') || '—'}
          </div>
          <button onclick="taskModal.show('edit', ${task.id})" 
                  class="bg-blue-600 text-white px-3 py-1 rounded mr-2">
            Редактировать
          </button>
          <button onclick="taskModal.show('delete', ${task.id})" 
                  class="bg-red-600 text-white px-3 py-1 rounded">
            Удалить
          </button>
        `;
    }

    // ИСПРАВЛЕНО: отображение имени задачи
    renderDelete(task) {
        this.body.innerHTML = `
          <h3 class="text-lg font-medium mb-4">Удалить задачу?</h3>
          <p class="mb-6">Вы уверены, что хотите удалить "${task.title}"?</p>
          <div class="flex justify-end gap-3">
            <button onclick="taskModal.hide()" 
                    class="px-4 py-2 border rounded">Отмена</button>
            <button onclick="taskModal.confirmDelete(${task.id})" 
                    class="px-4 py-2 bg-red-600 text-white rounded">
              Удалить
            </button>
          </div>
        `;
    }

    async confirmDelete(id) {
        try {
            await taskAPI.delete(id);
            this.hide();
            window.dispatchEvent(new CustomEvent('task-deleted'));
        } catch (err) {
            alert('Delete failed: ' + err.message);
        }
    }
}