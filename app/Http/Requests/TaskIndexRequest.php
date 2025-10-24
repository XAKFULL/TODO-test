<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskIndexRequest extends FormRequest
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
            'search' => 'nullable|string|min:3',
            'status' => 'nullable|in:pending,in_progress,completed',
            'tags' => 'nullable|array',
            'sort' => 'nullable|in:title,status,created_at,updated_at',
            'direction' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1',
        ];
    }
}
