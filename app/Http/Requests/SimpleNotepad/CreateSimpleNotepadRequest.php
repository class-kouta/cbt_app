<?php

namespace App\Http\Requests\SimpleNotepad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateSimpleNotepadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:10000'],
            'tag_ids' => ['nullable', 'array', 'max:10'],
            'tag_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('simple_notepad_tags', 'id')->where('member_id', Auth::id()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'メモ内容を入力してください',
            'content.max' => 'メモ内容は10000文字以内で入力してください',
            'tag_ids.max' => 'タグは10個まで選択できます',
        ];
    }
}
