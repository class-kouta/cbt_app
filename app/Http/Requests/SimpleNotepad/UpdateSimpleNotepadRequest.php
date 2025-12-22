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
            'content' => ['required', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'メモ内容を入力してください',
            'content.max' => 'メモ内容は10000文字以内で入力してください',
        ];
    }
}
