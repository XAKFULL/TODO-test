// components/taskForm.js
import {Tag} from "../models/Tag.js";
import {Task} from "../models/Task.js";

export class TaskForm {
    static render(task) {
        const tagsStr = (task.tags || []).map(t => t.name).join(', ');

        return `
          <form id="taskForm">
            <input type="hidden" id="taskId" value="${task.id || ''}">
            
            <label for="taskTitle">Название задачи *</label>
            <input type="text" id="taskTitle" value="${task.title || ''}" required>
            
            <label for="taskDescription">Описание</label>
            <textarea id="taskDescription">${task.description || ''}</textarea>
            
            <label for="taskStatus">Статус</label>
            <select id="taskStatus">
              <option value="pending" ${task.status === 'pending' ? 'selected' : ''}>В ожидании</option>
              <option value="in_progress" ${task.status === 'in_progress' ? 'selected' : ''}>В работе</option>
              <option value="completed" ${task.status === 'completed' ? 'selected' : ''}>Завершено</option>
            </select>
            
            <label for="taskTags">Теги (через запятую)</label>
            <input type="text" id="taskTags" value="${tagsStr}">
            
            <div class="flex justify-end gap-2 mt-4">
              <button type="button" onclick="taskModal.hide()" class="px-3 py-2 border rounded">Отмена</button>
              <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded">Сохранить</button>
            </div>
          </form>
        `;
    }

    static bind(task, onSave) {
        const form = document.getElementById('taskForm');
        form?.addEventListener('submit', (e) => {
            e.preventDefault();

            const id = document.getElementById('taskId').value || null;
            const idValue = id === '' ? null : (id ? Number(id) : null);

            const title = document.getElementById('taskTitle').value.trim();
            const description = document.getElementById('taskDescription').value;
            const status = document.getElementById('taskStatus').value;
            const tagInput = document.getElementById('taskTags').value;
            const tags = tagInput
                .split(',')
                .map(name => name.trim())
                .filter(name => name)
                .map(name => new Tag(name));

            if (!title) {
                alert('Укажите название задачи');
                return;
            }

            const updatedTask = new Task({
                id: idValue,
                title,
                description,
                status,
                tags
            });

            onSave(updatedTask);
        });
    }
}