<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagRequest extends FormRequest
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
        $tagId = $this->route('tag')?->id;

        return [
            'name' => ['required', 'string', 'min:2', 'max:50', Rule::unique('tags', 'name')->ignore($tagId)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tag name is required',
            'name.unique' => 'Tag with this name already exists',
            'name.max' => 'Tag name cannot exceed 50 characters',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Нормализуем имя тега (удаляем пробелы, приводим к нижнему регистру)
        $this->merge([
            'name' => trim(strtolower($this->input('name'))),
        ]);
    }
}
