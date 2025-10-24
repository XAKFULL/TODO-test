<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Нормализуем теги (удаляем пробелы, приводим к нижнему регистру)
        if ($this->has('tags')) {
            $this->merge([
                'tags' => collect($this->input('tags'))
                    ->map(fn ($tag) => trim(strtolower($tag)))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all(),
            ]);
        }
    }
}
