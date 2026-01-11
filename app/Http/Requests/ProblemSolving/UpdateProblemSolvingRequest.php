<?php

namespace App\Http\Requests\ProblemSolving;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProblemSolvingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'problem_situation' => ['required', 'string', 'max:5000'],
            'improved_image' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'problem_situation.required' => '問題状況を入力してください',
            'problem_situation.max' => '問題状況は5000文字以内で入力してください',
            'improved_image.max' => '改善イメージは2000文字以内で入力してください',
        ];
    }
}
