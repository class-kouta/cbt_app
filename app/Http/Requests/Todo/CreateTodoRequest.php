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
            'difficulty_id' => ['required', 'integer', 'min:1', 'exists:difficulties,id'],
            'content' => ['required', 'string', 'max:10000'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'min:1', 'distinct', 'exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'difficulty_id.required' => '難易度を選択してください',
            'difficulty_id.exists' => '選択された難易度は存在しません',
            'content.required' => '内容を入力してください',
            'tag_ids.*.exists' => '選択されたタグは存在しません',
        ];
    }
}
