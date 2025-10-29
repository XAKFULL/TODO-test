import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/tasks.css',
                'resources/js/app.js',
                'resources/js/tasks/main.js',
                'resources/js/components/kanban.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
