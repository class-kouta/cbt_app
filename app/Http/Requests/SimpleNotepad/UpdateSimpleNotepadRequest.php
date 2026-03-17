<?php

namespace App\Http\Requests\SimpleNotepad;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSimpleNotepadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'タイトルは255文字以内で入力してください',
            'content.required' => 'メモ内容を入力してください',
            'content.max' => 'メモ内容は10000文字以内で入力してください',
        ];
    }
}
