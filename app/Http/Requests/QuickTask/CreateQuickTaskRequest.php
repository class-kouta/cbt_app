<?php

namespace App\Http\Requests\QuickTask;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuickTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:200'],
            'difficulty_id' => ['sometimes', 'nullable', 'integer', 'exists:difficulties,id'],
            'tag_ids' => ['sometimes', 'array'],
            'tag_ids.*' => ['integer', 'min:1', 'distinct', 'exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'クイックタスクの内容を入力してください',
            'content.max' => 'クイックタスクの内容は200文字以内で入力してください',
            'difficulty_id.exists' => '選択された難易度は存在しません',
            'tag_ids.*.exists' => '選択されたタグは存在しません',
        ];
    }
}
