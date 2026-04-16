<?php

namespace App\Http\Requests\Task;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in(Task::STATUSES)],
            'due_date' => ['nullable', 'date'],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
