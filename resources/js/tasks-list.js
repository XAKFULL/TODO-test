document.addEventListener('DOMContentLoaded', () => {
    window.loadTasks(1);
    window.loadAllTags();
    window.initTagsInput();
});

document.addEventListener('change', (e) => {
    if (e.target.id === 'sort-by') {
        window.loadTasks(1); // сброс на 1-ю страницу при смене сортировки
    }
});

let allTags = []; // глобальный список всех тегов (имена)
let selectedTags = []; // выбранные теги (имена)

function showTagSuggestions(query = '') {
    const suggestions = document.getElementById('tag-suggestions');
    if (!suggestions) return;

    const lowerQuery = query.toLowerCase();
    const availableTags = allTags.filter(tag => !selectedTags.includes(tag));

    let matches = availableTags;
    if (lowerQuery) {
        matches = availableTags.filter(tag => tag.toLowerCase().includes(lowerQuery));
    }

    matches = matches.slice(0, 10); // максимум 10

    if (matches.length > 0) {
        suggestions.innerHTML = '';
        matches.forEach(tag => {
            const div = document.createElement('div');
            div.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm';
            div.textContent = tag;
            div.addEventListener('click', () => {
                addTag(tag);
                document.getElementById('tag-input').value = '';
                suggestions.classList.add('hidden');
                document.getElementById('tag-input').focus(); // вернуть фокус
            });
            suggestions.appendChild(div);
        });
        suggestions.classList.remove('hidden');
    } else {
        suggestions.classList.add('hidden');
    }
}
window.initTagsInput = function () {
    const input = document.getElementById('tag-input');
    const suggestions = document.getElementById('tag-suggestions');
    const selectedContainer = document.getElementById('selected-tags');

    if (!input || !suggestions) return;

    // Показать все теги при фокусе
    input.addEventListener('focus', () => {
        showTagSuggestions(''); // пустая строка = все теги
    });

    // Фильтровать при вводе
    input.addEventListener('input', () => {
        const value = input.value.trim().toLowerCase();
        showTagSuggestions(value);
    });

    // Скрыть при потере фокуса (но с задержкой, чтобы успеть кликнуть)
    input.addEventListener('blur', () => {
        setTimeout(() => {
            suggestions.classList.add('hidden');
        }, 150);
    });

    // Скрыть при клике вне
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#tag-input') && !e.target.closest('#tag-suggestions')) {
            suggestions.classList.add('hidden');
        }
    });
}

function addTag(tagName) {
    if (selectedTags.includes(tagName)) return;

    selectedTags.push(tagName);
    renderSelectedTags();
    // Автоматически применяем фильтр
    if (typeof window.loadTasks === 'function') {
        window.loadTasks(1);
    }
}

window.removeTag = function (tagName) {
    selectedTags = selectedTags.filter(t => t !== tagName);
    renderSelectedTags();
    if (typeof window.loadTasks === 'function') {
        window.loadTasks(1);
    }
}

function renderSelectedTags() {
    const container = document.getElementById('selected-tags');
    if (!container) return;

    container.innerHTML = '';
    selectedTags.forEach(tag => {
        const chip = document.createElement('span');
        chip.className = 'bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded flex items-center';
        chip.innerHTML = `
            ${tag}
            <button type="button" class="ml-1 text-blue-600 hover:text-blue-900" onclick="removeTag('${tag.replace(/'/g, "\\'")}')">
                &times;
            </button>
        `;
        container.appendChild(chip);
    });
}

// Загрузка всех тегов при старте
window.loadAllTags = function () {
    fetch('/api/tags')
        .then(res => res.json())
        .then(response => {
            let tags = response.data;
            allTags = tags.map(t => t.name); // сохраняем только имена
            console.log('Все теги:', allTags);
        })
        .catch(err => console.error('Ошибка загрузки тегов:', err));
}

// Загрузка задач с фильтрами
window.loadTasks = function(page = 1) {
    const search = document.getElementById('search-input').value;
    const status = document.getElementById('status-filter').value;
    const perPage = document.getElementById('per-page').value;
    const sortByValue = document.getElementById('sort-by')?.value || 'created_at|desc';

    const [sort_by, sort_dir] = sortByValue.split('|');

    const payload = {
        page: page,
        per_page: perPage,
        search: search || undefined,
        status: status || undefined,
        ...(selectedTags.length > 0 && { tags: selectedTags }),
        sort: sort_by,
        direction: sort_dir,
        with_tags: true
    };

    Object.keys(payload).forEach(key => payload[key] === undefined && delete payload[key]);

    fetch('/api/tasks-paginated', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
        .then(res => {
            if (!res.ok) throw new Error('Ошибка загрузки');
            return res.json();
        })
        .then(data => {
            renderTasks(data.data);
            renderPagination(data);
        })
        .catch(err => {
            console.error(err);
            document.getElementById('tasks-table-body').innerHTML =
                `<tr><td colspan="4" class="px-6 py-4 text-center text-red-500">Ошибка загрузки</td></tr>`;
        });
}

// Рендер задач
window.renderTasks = function(tasks) {
    const tbody = document.getElementById('tasks-table-body');
    tbody.innerHTML = '';

    if (tasks.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Задач не найдено</td></tr>`;
        return;
    }

    tasks.forEach(task => {
        const statusLabels = {
            'pending': 'Ожидает',
            'in_progress': 'В работе',
            'completed': 'Завершено'
        };

        const tagsHtml = task.tags?.map(t =>
            `<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">${t}</span>`
        ).join('') || '<span class="text-gray-500">—</span>';

        const row = `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="font-medium">${task.id}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="font-medium">${task.title}</div>
                    <div class="text-sm text-gray-500 mt-1">${task.description || ''}</div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs ${
            task.status_code === 'completed' ? 'bg-green-100 text-green-800' :
                task.status_code === 'in_progress' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-gray-100 text-gray-800'
        }">
                        ${statusLabels[task.status_code] || task.status_code}
                    </span>
                </td>
                <td class="px-6 py-4">${tagsHtml}</td>
                <td class="px-6 py-4">
                    <button type="button" class="text-blue-500 text-sm mr-2"
                            onclick="openTaskDetail(${task.id})">
                        Открыть
                    </button>
                    <button type="button" class="text-blue-500 text-sm mr-2"
                            onclick="openEditModal(${task.id})">
                        Редактировать
                    </button>
                    <button type="button" class="text-red-500 text-sm"
                            onclick="openDeleteModal(${ task.id }, ${ task.title })">
                        Удалить
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

// Рендер пагинации
window.renderPagination = function(data)  {
    const pagination = document.getElementById('pagination');
    const { current_page, last_page, total } = data.meta;

    let html = `<div class="text-sm text-gray-600">Всего: ${total}</div>`;

    if (last_page > 1) {
        const prevDisabled = current_page === 1 ? 'disabled' : '';
        const nextDisabled = current_page === last_page ? 'disabled' : '';

        html += `
            <div class="flex space-x-2">
                <button ${prevDisabled} onclick="loadTasks(${current_page - 1})"
                        class="px-3 py-1 border rounded ${prevDisabled ? 'text-gray-400' : 'hover:bg-gray-100'}">
                    Назад
                </button>
                <span class="px-3 py-1">Стр. ${current_page} из ${last_page}</span>
                <button ${nextDisabled} onclick="loadTasks(${current_page + 1})"
                        class="px-3 py-1 border rounded ${nextDisabled ? 'text-gray-400' : 'hover:bg-gray-100'}">
                    Вперёд
                </button>
            </div>
        `;
    }

    pagination.innerHTML = html;
}

// Слушатели фильтров
document.getElementById('search-input')?.addEventListener('input', () => loadTasks());
document.getElementById('status-filter')?.addEventListener('change', () => loadTasks());
document.getElementById('tag-filter')?.addEventListener('change', () => loadTasks());
document.getElementById('per-page')?.addEventListener('change', () => loadTasks());
