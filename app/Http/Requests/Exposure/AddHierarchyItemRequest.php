<?php

namespace App\Http\Requests\Exposure;

use App\Rules\SudsScore;
use Illuminate\Foundation\Http\FormRequest;

class AddHierarchyItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'expected_suds' => ['nullable', 'integer', new SudsScore],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => '状況の内容を入力してください',
            'content.max' => '状況の内容は500文字以内で入力してください',
        ];
    }
}
