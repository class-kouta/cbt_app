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
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => [
                'integer',
                Rule::exists('simple_notepad_tags', 'id')->where('member_id', Auth::id()),
            ],
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
