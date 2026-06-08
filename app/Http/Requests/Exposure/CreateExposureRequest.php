<?php

namespace App\Http\Requests\Exposure;

use Illuminate\Foundation\Http\FormRequest;

class CreateExposureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'avoidance_target' => ['required', 'string', 'max:5000'],
            'self_talk' => ['nullable', 'string', 'max:2000'],
            'overall_reflection' => ['nullable', 'string', 'max:5000'],
            'next_goal' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'avoidance_target.required' => '回避していることを入力してください',
            'avoidance_target.max' => '回避していることは5000文字以内で入力してください',
            'self_talk.max' => '自分への声かけは2000文字以内で入力してください',
            'overall_reflection.max' => '全体振り返りは5000文字以内で入力してください',
            'next_goal.max' => '次の目標は2000文字以内で入力してください',
        ];
    }
}
