<?php

namespace App\Http\Requests\ModeDialogueWork;

use App\Enums\ModeCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateModeDialogueWorkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:50000'],
            'mode_category' => ['required', 'string', Rule::in(ModeCategory::values())],
            'mode_name' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => '対話ワーク内容を入力してください',
            'content.max' => '対話ワーク内容は50000文字以内で入力してください',
            'mode_category.required' => 'モードカテゴリを選択してください',
            'mode_category.in' => '無効なモードカテゴリです',
            'mode_name.required' => 'モード名を入力してください',
            'mode_name.max' => 'モード名は100文字以内で入力してください',
        ];
    }
}
