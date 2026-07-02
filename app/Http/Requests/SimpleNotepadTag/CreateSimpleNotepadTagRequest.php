<?php

namespace App\Http\Requests\SimpleNotepadTag;

use App\Enums\SimpleNotepadTagColor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateSimpleNotepadTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:10',
                Rule::unique('simple_notepad_tags', 'name')->where('member_id', Auth::id()),
            ],
            'color' => [
                'nullable',
                'string',
                Rule::in(SimpleNotepadTagColor::values()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'タグ名を入力してください',
            'name.max' => 'タグ名は10文字以内で入力してください',
            'name.unique' => '同じ名前のタグが既に存在します',
            'color.in' => '選択できない色です',
        ];
    }
}
