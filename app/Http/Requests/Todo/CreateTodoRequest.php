<?php

namespace App\Http\Requests\Todo;

use Illuminate\Foundation\Http\FormRequest;

class CreateTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'difficulty_id' => ['required', 'integer', 'min:1'],
            'content' => ['required', 'string', 'max:10000'],
            'tag_ids' => ['sometimes', 'array'],
            'tag_ids.*' => ['integer', 'min:1'],
        ];
    }
}

