// Утилиты модалок
window.openModal = function(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
};

window.closeModal = function(modalId) {
    document.getElementById(modalId).classList.add('hidden');
};

// Закрытие по клику вне
document.addEventListener('click', function (e) {
    // Ищем ближайший родитель с классом .task-modal
    const modalOverlay = e.target.closest('.task-modal');
    if (!modalOverlay) return;

    // Проверяем: кликнули ли ВНУТРЬ содержимого модалки?
    const modalContent = e.target.closest('.modal-content');
    if (!modalContent) {
        // Клик был на оверлее (фоне) → закрываем
        const modalId = modalOverlay.id;
        if (window.closeModal) {
            window.closeModal(modalId);
        }
    }
});
// === УДАЛЕНИЕ ===
// Текущий ID задачи для удаления
let taskIdToDelete = null;

// Открыть модалку удаления
window.openDeleteModal = function(taskId, taskTitle = 'эту задачу') {
    taskIdToDelete = taskId;
    document.getElementById('delete-task-title').textContent =
        `Вы уверены, что хотите удалить задачу «${taskTitle}»?`;
    openModal('deleteTaskModal');
};

// Подтверждение удаления
document.getElementById('confirm-delete-btn')?.addEventListener('click', function() {
    if (!taskIdToDelete) return;

    fetch(`/api/tasks/${taskIdToDelete}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
        .then(response => {
            if (response.ok) {
                // Удаляем карточку из канбана
                const card = document.querySelector(`.task-card[data-task-id="${taskIdToDelete}"]`);
                if (card) card.remove();

                // Удаляем строку из списка
                const row = document.querySelector(`#task-row-${taskIdToDelete}`);
                if (row) row.remove();

                closeModal('deleteTaskModal');
                taskIdToDelete = null;
            } else {
                alert('Ошибка при удалении');
            }
        })
        .catch(() => {
            alert('Не удалось удалить задачу');
        });
});

// === РЕДАКТИРОВАНИЕ ===
window.openEditModal = function(taskId) {

    fetch(`/api/tasks/${taskId}`)
        .then(response => response.json())
        .then(json => {
            const task = json.data || json;

            // Заполняем основные поля
            document.getElementById('edit-task-id').value = task.id;
            document.getElementById('edit-task-title').value = task.title || '';
            document.getElementById('edit-task-description').value = task.description || '';
            document.getElementById('edit-task-status').value = task.status_code || 'pending';

            // Очищаем контейнер тегов
            const container = document.getElementById('edit-tags-container');
            container.innerHTML = '';

            // Добавляем существующие теги
            const tagNames = task.tags?.map(t => t) || [];
            tagNames.forEach(tag => {
                addTagInput(tag);
            });

            // Добавляем одну пустую строку, если нет тегов
            if (tagNames.length === 0) {
                addTagInput();
            }

            openModal('editTaskModal');
        })
        .catch(err => {
            console.error('Ошибка загрузки задачи:', err);
            alert('Не удалось загрузить данные задачи');
        });
};

function addTagInput(value = '') {
    const container = document.getElementById('edit-tags-container');
    const index = container.children.length;

    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2';
    div.innerHTML = `
        <input type="text"
               name="tags[]"
               value="${value}"
               placeholder="Введите тег"
               class="flex-1 border rounded px-3 py-2 text-sm">
        <button type="button" class="text-red-500 text-sm remove-tag-btn">&times;</button>
    `;
    container.appendChild(div);

    // Обработчик удаления
    div.querySelector('.remove-tag-btn').addEventListener('click', () => {
        div.remove();
        // Если удалили последнее поле — добавим пустое
        if (container.children.length === 0) {
            addTagInput();
        }
    });
}

// Обработчик кнопки "+ Добавить тег"
document.getElementById('add-tag-btn')?.addEventListener('click', () => {
    addTagInput();
});
// Обработка формы редактирования
document.getElementById('editTaskForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const taskId = document.getElementById('edit-task-id').value;
    const title = document.getElementById('edit-task-title').value;
    const description = document.getElementById('edit-task-description').value;
    const status = document.getElementById('edit-task-status').value;

    // Собираем теги (игнорируем пустые)
    const tagInputs = document.querySelectorAll('#edit-tags-container input[name="tags[]"]');
    const tags = Array.from(tagInputs)
        .map(input => input.value.trim())
        .filter(tag => tag !== '');

    const data = {
        title,
        description,
        status_code: status,
        tags: tags
    };

    fetch(`/api/tasks/${taskId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => {
            if (response.ok) {
                closeModal('editTaskModal');
                // Обновим данные в канбане или списке (опционально)
            } else {
                return response.text().then(text => { throw new Error(text); });
            }
        })
        .catch(err => {
            console.error('Ошибка обновления:', err);
            alert('Не удалось сохранить задачу');
        });
});

// === СОЗДАНИЕ ===
document.getElementById('createTaskForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const data = {
        title: document.getElementById('create-task-title').value,
        description: document.getElementById('create-task-description').value,
        status: document.getElementById('create-task-status').value
    };

    fetch('/api/tasks', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(response => {
            let task = response.data;
            // Создаём новую карточку вручную (только после успешного создания)
            const card = document.createElement('div');
            card.className = 'bg-gray-50 p-3 rounded border border-gray-200 task-card';
            card.dataset.taskId = task.id;
            card.dataset.status = task.status_code;
            card.innerHTML = `
            <h3 class="font-medium">${task.title}</h3>
            <p class="text-sm text-gray-600 mt-1">${task.description || ''}</p>
            <div class="mt-2 flex space-x-2">
                <button type="button" class="text-blue-500 text-sm" onclick="openEditModal(${task.id})">
                    Редактировать
                </button>
                <button type="button" class="text-red-500 text-sm" onclick="deleteTask(${task.id})">
                    Удалить
                </button>
            </div>
        `;
            document.getElementById(`column-${task.status_code}`).appendChild(card);
            closeModal('createTaskModal');
            // Сброс формы
            document.getElementById('createTaskForm').reset();
        })
        .catch(() => alert('Не удалось создать задачу'));
});


// === ТЕГИ: наведение и открытие задачи ===

let hoverTimeout = 0.1;

document.addEventListener('mouseover', function (e) {
    const card = e.target.closest('.task-card');
    if (!card) return;

    const tooltip = card.querySelector('.task-tags-tooltip');
    if (!tooltip) return;

    const taskId = card.dataset.taskId;

    // Покажем тултип через 300 мс (чтобы не мелькало)
    hoverTimeout = setTimeout(() => {
        tooltip.textContent = 'Загрузка...';
        tooltip.classList.remove('hidden');

        // Загружаем теги (кэшируем, чтобы не грузить каждый раз)
        if (!card.dataset.tagsLoaded) {
            fetch(`/api/tasks/${taskId}`)
                .then(res => res.json())
                .then(response => {
                    let task = response.data;

                    if (!task.tags || task.tags.length === 0) {
                        tooltip.textContent = 'Нет тегов';
                    } else {
                        const tagNames = task.tags.map(t => t).join(', ');
                        tooltip.textContent = tagNames;
                    }
                    card.dataset.tagsLoaded = 'true'; // кэш в DOM
                })
                .catch(() => {
                    tooltip.textContent = 'Ошибка';
                });
        } else {
            // Если уже загружены — покажем из кэша (опционально можно хранить в dataset)
            // Пока просто оставим как есть — можно улучшить позже
        }
    }, 300);
});

document.addEventListener('mouseout', function (e) {
    if (hoverTimeout) {
        clearTimeout(hoverTimeout);
        hoverTimeout = null;
    }

    const card = e.target.closest('.task-card');
    if (!card) return;

    const tooltip = card.querySelector('.task-tags-tooltip');
    if (tooltip) {
        tooltip.classList.add('hidden');
    }
});

// === Открытие задачи по клику на карточку или кнопку ===

document.addEventListener('click', function (e) {
    const openBtn = e.target.closest('.task-card');
    if (!openBtn) return;

    const cardButton = e.target.closest('.card-button');
    if (cardButton) return;

    const taskId = openBtn.dataset.taskId;
    openTaskDetail(taskId);
});

// Новая функция: открыть детали задачи
window.openTaskDetail = function(taskId) {
    fetch(`/api/tasks/${taskId}`)
        .then(res => res.json())
        .then(response => {
            let task = response.data;
            // Создаём или обновляем модалку деталей
            showTaskDetailModal(task);
        })
        .catch(() => alert('Не удалось загрузить задачу'));
};

function showTaskDetailModal(task) {
    document.getElementById('detail-task-id').value = task.id;
    document.getElementById('detail-task-title').textContent = task.title;
    document.getElementById('detail-task-description').textContent = task.description || '—';

    // Статус: pending → "Ожидает" и т.д.
    const statusLabels = {
        'pending': 'Ожидает',
        'in_progress': 'В работе',
        'completed': 'Завершено'
    };
    document.getElementById('detail-task-status').textContent = statusLabels[task.status_code] || task.status_code;

    // Теги
    const tagsContainer = document.getElementById('detail-task-tags');
    tagsContainer.innerHTML = '';
    if (task.tags && task.tags.length > 0) {
        task.tags.forEach(tag => {
            const tagEl = document.createElement('span');
            tagEl.className = 'bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded';
            tagEl.textContent = tag;
            tagsContainer.appendChild(tagEl);
        });
    } else {
        tagsContainer.innerHTML = '<span class="text-gray-500 text-sm">Нет тегов</span>';
    }

    openModal('taskDetailModal');
}

// === DRAG AND DROP ===
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.task-card').forEach(card => {
        card.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', card.dataset.taskId);
            card.classList.add('opacity-50');
        });
        card.addEventListener('dragend', () => {
            card.classList.remove('opacity-50');
        });
    });

    // Обработчики НА КОЛОНКАХ (даже если пустые!)
    document.querySelectorAll('[id^="column-body-"]').forEach(column => {
        column.addEventListener('dragover', (e) => {
            e.preventDefault(); // ← обязательно!
            column.classList.add('bg-blue-50', 'border-dashed', 'border-2');
        });

        column.addEventListener('dragleave', (e) => {
            // Чтобы не "мигало", проверим, что вышли за пределы колонки
            if (!column.contains(e.relatedTarget)) {
                column.classList.remove('bg-blue-50', 'border-dashed', 'border-2');
            }
        });

        column.addEventListener('drop', (e) => {
            e.preventDefault();
            column.classList.remove('bg-blue-50', 'border-dashed', 'border-2');

            const taskId = e.dataTransfer.getData('text/plain');
            const newStatus = column.id.replace('column-body-', '');

            const card = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
            if (!card) return;

            const oldStatus = card.dataset.status;
            if (oldStatus === newStatus) return;

            updateTaskStatus(taskId, newStatus)
                .then(updatedTask => {
                    card.dataset.status = newStatus;
                    column.appendChild(card);
                })
                .catch(err => {
                    console.error('Ошибка при обновлении статуса:', err);
                    alert('Не удалось переместить задачу');
                });
        });
    });
});

// Функция обновления статуса через API
async function updateTaskStatus(taskId, newStatus) {
    try {
        const getResponse = await fetch(`/api/tasks/${taskId}`);
        if (!getResponse.ok) throw new Error('Не удалось загрузить задачу');

        const json = await getResponse.json();
        const task = json.data || json; // поддержка и с data, и без
        task.status = newStatus;

        const updateResponse = await fetch(`/api/tasks/${taskId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(task)
        });

        if (!updateResponse.ok) {
            const errorText = await updateResponse.text();
            console.error('Ошибка обновления:', errorText);
            throw new Error('Сервер вернул ошибку при обновлении');
        }

        const updatedJson = await updateResponse.json();
        return updatedJson.data || updatedJson;

    } catch (err) {
        console.error('updateTaskStatus failed:', err);
        alert('Не удалось изменить статус задачи');
        throw err; // чтобы вызывающий код знал об ошибке
    }
}
