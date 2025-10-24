<form id="task-form" novalidate>
    @csrf
    @if(isset($task))
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" name="title" id="title" class="form-control"
               value="{{ old('title', $task->title ?? '') }}" required>
        <div class="invalid-feedback">Please enter a title</div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" class="form-control" rows="4">
            {{ old('description', $task->description ?? '') }}
        </textarea>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                @foreach (['pending', 'in_progress', 'completed'] as $status)
                    <option value="{{ $status }}"
                        {{ (old('status', $task->status ?? 'pending') == $status) ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="tags" class="form-label">Tags</label>
            <select name="tags[]" id="tags" class="form-select" multiple required></select>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-times me-1"></i>Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>{{ isset($task) ? 'Update Task' : 'Create Task' }}
        </button>
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Инициализация Select2 для тегов
            $('#tags').select2({
                ajax: {
                    url: "{{ route('api.tags.index') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.data.map(tag => ({
                                id: tag.name,
                                text: tag.name
                            })),
                            pagination: {
                                more: data.meta ? data.meta.current_page < data.meta.last_page : false
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Select or create tags',
                minimumInputLength: 1,
                tags: true,
                createTag: function(params) {
                    return {
                        id: params.term,
                        text: params.term,
                        newTag: true
                    };
                },
                templateResult: function(tag) {
                    if (tag.loading) return tag.text;

                    let $result = $('<span></span>');
                    $result.text(tag.text);

                    if (tag.newTag) {
                        $result.append(' <small class="text-muted">(new)</small>');
                    }

                    return $result;
                }
            });

            // Предзагрузка выбранных тегов для редактирования
            @if(isset($task) && $task->tags->count())
            $('#tags').val({!! json_encode($task->tags->pluck('name')) !!}).trigger('change');
            @endif

            // Обработка отправки формы
            const form = document.getElementById('task-form');
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = {
                    title: document.getElementById('title').value,
                    description: document.getElementById('description').value,
                    status: document.getElementById('status').value,
                    tags: $('#tags').val()
                };

                const url = @json(isset($task) ? route('api.tasks.update', $task->id) : route('api.tasks.store'));
                const method = @json(isset($task) ? 'PUT' : 'POST');

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.Laravel.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (data.errors) {
                            // Обработка ошибок валидации
                            Object.keys(data.errors).forEach(field => {
                                const input = document.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    input.nextElementSibling.textContent = data.errors[field][0];
                                }
                            });
                            return;
                        }
                        throw new Error(data.message || 'Something went wrong');
                    }

                    // Успешное действие
                    showToast('success', data.message || 'Task saved successfully');

                    // Перенаправление через 1.5 сек
                    setTimeout(() => {
                        window.location.href = "{{ route('tasks.index') }}";
                    }, 1500);

                } catch (error) {
                    showToast('danger', error.message);
                }
            });

            // Сброс ошибок при вводе
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    this.nextElementSibling.textContent = '';
                });
            });

            // Функция для показа уведомлений
            function showToast(type, message) {
                const toast = document.createElement('div');
                toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');
                toast.setAttribute('aria-atomic', 'true');

                toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

                document.body.appendChild(toast);

                const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
                bsToast.show();

                toast.addEventListener('hidden.bs.toast', () => {
                    toast.remove();
                });
            }
        });
    </script>
@endpush
