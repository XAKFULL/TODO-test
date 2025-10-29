import { Tag } from './Tag.js';

export class Task {
    constructor(data = {}) {
        this.id = data.id || null;
        this.title = data.title || '';
        this.description = data.description || '';
        this.status = data.status || 'pending';
        this.status_label = data.status_label || this.getStatusLabel();
        this.created_at = data.created_at ? new Date(data.created_at) : new Date();
        this.updated_at = data.updated_at ? new Date(data.updated_at) : new Date();

        // Преобразуем теги
        this.tags = Array.isArray(data.tags)
            ? data.tags.map(tag => tag instanceof Tag ? tag : new Tag(tag))
            : [];
    }

    getStatusLabel() {
        const labels = {
            'pending': 'В ожидании',
            'in_progress': 'В работе',
            'completed': 'Завершено'
        };
        return labels[this.status] || this.status;
    }

    toDTO() {
        return {
            title: this.title,
            description: this.description || null,
            status: this.status,
            tags: this.tags.map(tag => tag.name)
        };
    }
}