import { Task } from '../models/Task.js';

export const taskAPI = {
    async get(id) {
        const res = await fetch(`/api/tasks/${id}`);
        if (!res.ok) throw new Error('Task not found');
        const data = await res.json();
        return new Task(data.data);
    },

    async save(task) {
        const method = task.id ? 'PUT' : 'POST';
        const url = task.id ? `/api/tasks/${task.id}` : '/api/tasks';
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(task.toDTO())
        });
        if (!res.ok) throw new Error('Failed to save task');
        const data = await res.json();
        return new Task(data);
    },

    async delete(id) {
        const res = await fetch(`/api/tasks/${id}`, { method: 'DELETE' });
        if (!res.ok) throw new Error('Failed to delete task');
        return true;
    },

    async updateStatus(id, status) {
        const res = await fetch(`/api/tasks/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status })
        });
        if (!res.ok) throw new Error('Failed to update status');
        return res.json();
    },

    async paginate(filters = {}) {
        const res = await fetch('/api/tasks-paginated', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                ...filters,
                tags: filters.tags?.length ? filters.tags : undefined,
                page: filters.page || 1,
                per_page: filters.per_page || 15,
                with_tags: true
            })
        });

        if (!res.ok) {
            const error = await res.json().catch(() => ({ message: 'Failed to load tasks' }));
            throw new Error(error.message || 'Failed to load tasks');
        }

        const response = await res.json();

        return {
            data: response.data.map(task => new Task(task)),
            meta: response.meta
        };
    }
};