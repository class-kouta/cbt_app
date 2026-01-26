<?php

namespace App\Http\Requests\ProblemSolving;

use Illuminate\Foundation\Http\FormRequest;

class AddSolutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'effectiveness' => ['nullable', 'integer', 'min:0', 'max:100'],
            'feasibility' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => '解決策の内容を入力してください',
            'content.max' => '解決策の内容は100文字以内で入力してください',
            'sort_order.required' => '表示順を指定してください',
            'sort_order.min' => '表示順は1以上を指定してください',
            'effectiveness.min' => '効果は0以上を指定してください',
            'effectiveness.max' => '効果は100以下を指定してください',
            'feasibility.min' => '実行可能性は0以上を指定してください',
            'feasibility.max' => '実行可能性は100以下を指定してください',
        ];
    }
}
