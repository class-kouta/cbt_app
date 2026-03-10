<?php

namespace App\Http\Requests\DialogueWork;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDialogueWorkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:50000'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => '対話ワーク内容を入力してください',
            'content.max' => '対話ワーク内容は50000文字以内で入力してください',
        ];
    }
}
