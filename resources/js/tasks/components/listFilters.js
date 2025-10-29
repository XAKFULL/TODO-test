// components/listFilters.js
export class ListFilters {
    constructor() {
        this.filters = {
            search: '',
            status: '',
            tags: [],
            sort: 'created_at',
            direction: 'desc',
            page: 1,
            per_page: 15
        };

        this.elements = {
            search: document.getElementById('search-input'),
            status: document.getElementById('status-filter'),
            tagsInput: document.getElementById('tag-input'),
            selectedTags: document.getElementById('selected-tags'),
            suggestions: document.getElementById('tag-suggestions'),
            sortSelect: document.getElementById('sort-by'),
            perPage: document.getElementById('per-page')
        };

        this.initEventListeners();
        this.parseSortValue();
        this.initTagAutocomplete();
    }

    initEventListeners() {
        // Поиск
        this.elements.search?.addEventListener('input', (e) => {
            this.setFilter('search', e.target.value);
            this.resetPage();
            this.dispatchChange();
        });

        // Статус - ИСПРАВЛЕНО
        this.elements.status?.addEventListener('change', (e) => {
            const value = e.target.value;
            console.log('Status changed to:', value); // Для отладки
            this.setFilter('status', value);
            this.resetPage();
            this.dispatchChange();
        });

        // Сортировка
        this.elements.sortSelect?.addEventListener('change', (e) => {
            const [sort, direction] = e.target.value.split('|');
            this.setFilter('sort', sort);
            this.setFilter('direction', direction);
            this.resetPage();
            this.dispatchChange();
        });

        // Элементов на страницу
        this.elements.perPage?.addEventListener('change', (e) => {
            this.setFilter('per_page', parseInt(e.target.value));
            this.resetPage();
            this.dispatchChange();
        });
    }

    initTagAutocomplete() {
        const { tagsInput, suggestions, selectedTags } = this.elements;
        if (!tagsInput || !suggestions || !selectedTags) return;

        let allTags = [];
        let selectedTagsList = [];

        // Загрузка тегов
        const loadTags = async () => {
            try {
                const response = await fetch('/api/tags');
                if (response.ok) {
                    const data = await response.json();
                    allTags = data.data.map(tag => tag.name);
                }
            } catch (error) {
                console.error('Failed to load tags:', error);
            }
        };

        loadTags();

        // Показ подсказок
        const showSuggestions = (query) => {
            suggestions.classList.add('hidden');
            if (!query) return;

            const filtered = allTags.filter(tag =>
                tag.toLowerCase().includes(query.toLowerCase())
            ).slice(0, 5);

            if (filtered.length === 0) return;

            suggestions.innerHTML = filtered.map(tag => `
                <div class="p-2 cursor-pointer hover:bg-gray-100">${tag}</div>
            `).join('');

            suggestions.classList.remove('hidden');
        };

        // Добавление тега
        const addTag = (tag) => {
            if (!tag || selectedTagsList.includes(tag)) return;

            selectedTagsList.push(tag);
            this.setFilter('tags', selectedTagsList);
            this.resetPage();
            this.dispatchChange();

            updateSelectedTags();
            tagsInput.value = '';
            suggestions.classList.add('hidden');
        };

        // Обновление отображения выбранных тегов
        const updateSelectedTags = () => {
            selectedTags.innerHTML = selectedTagsList.map(tag => `
                <span class="inline-flex items-center bg-gray-100 px-2 py-1 rounded mr-1 mb-1">
                    ${tag}
                    <button type="button" class="ml-1 text-gray-500 hover:text-gray-700" 
                            data-tag="${tag}">×</button>
                </span>
            `).join('');

            // Обработчики удаления
            selectedTags.querySelectorAll('button[data-tag]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const tag = e.target.dataset.tag;
                    selectedTagsList = selectedTagsList.filter(t => t !== tag);
                    this.setFilter('tags', selectedTagsList);
                    this.resetPage();
                    this.dispatchChange();
                    updateSelectedTags();
                });
            });
        };

        // Обработчики
        tagsInput.addEventListener('input', (e) => {
            showSuggestions(e.target.value);
        });

        tagsInput.addEventListener('blur', () => {
            setTimeout(() => suggestions.classList.add('hidden'), 200);
        });

        tagsInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && tagsInput.value) {
                addTag(tagsInput.value);
            }
        });

        suggestions.addEventListener('click', (e) => {
            if (e.target.classList.contains('cursor-pointer')) {
                addTag(e.target.textContent);
            }
        });

        // Инициализация
        updateSelectedTags();
    }

    parseSortValue() {
        const sortValue = this.elements.sortSelect?.value || 'created_at|desc';
        const [sort, direction] = sortValue.split('|');
        this.setFilter('sort', sort);
        this.setFilter('direction', direction);
    }

    setFilter(key, value) {
        this.filters[key] = value;
    }

    getFilters() {
        return {
            ...this.filters,
            tags: this.filters.tags.length ? this.filters.tags : undefined
        };
    }

    resetPage() {
        this.setFilter('page', 1);
    }

    dispatchChange() {
        window.dispatchEvent(new CustomEvent('filters-changed', {
            detail: this.getFilters()
        }));
    }
}