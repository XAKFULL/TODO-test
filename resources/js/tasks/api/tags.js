import { Tag } from '../models/Tag.js';

export const tagAPI = {
    async get(id) {
        const res = await fetch(`/api/tags/${id}`);
        if (!res.ok) throw new Error('Tag not found');
        const data = await res.json();
        return new Tag(data);
    },

    async save(tag) {
        const method = tag.id ? 'PUT' : 'POST';
        const url = tag.id ? `/api/tags/${tag.id}` : '/api/tags';
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(tag.toDTO())
        });
        if (!res.ok) throw new Error('Failed to save tag');
        const data = await res.json();
        return new Tag(data);
    },

    async delete(id) {
        const res = await fetch(`/api/tags/${id}`, { method: 'DELETE' });
        if (!res.ok) throw new Error('Failed to delete tag');
        return true;
    },

    async all() {
        const res = await fetch('/api/tags');
        if (!res.ok) throw new Error('Failed to load tags');
        const data = await res.json();
        return data.data.map(tag => new Tag(tag));
    }
};