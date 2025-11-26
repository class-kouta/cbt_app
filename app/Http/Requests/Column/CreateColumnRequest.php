<?php

namespace App\Http\Requests\Column;

use Illuminate\Foundation\Http\FormRequest;

class CreateColumnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'situation' => ['required', 'string', 'max:1000'],
            'mood' => ['required', 'string', 'max:500'],
            'automatic_thought' => ['required', 'string', 'max:1000'],
            'evidence' => ['required', 'string', 'max:1000'],
            'counter_evidence' => ['required', 'string', 'max:1000'],
            'adaptive_thought' => ['required', 'string', 'max:1000'],
            'current_mood' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'situation.required' => '状況を入力してください',
            'situation.max' => '状況は1000文字以内で入力してください',
            'mood.required' => '気分を入力してください',
            'mood.max' => '気分は500文字以内で入力してください',
            'automatic_thought.required' => '自動思考を入力してください',
            'automatic_thought.max' => '自動思考は1000文字以内で入力してください',
            'evidence.required' => '根拠を入力してください',
            'evidence.max' => '根拠は1000文字以内で入力してください',
            'counter_evidence.required' => '反証を入力してください',
            'counter_evidence.max' => '反証は1000文字以内で入力してください',
            'adaptive_thought.required' => '適応的思考を入力してください',
            'adaptive_thought.max' => '適応的思考は1000文字以内で入力してください',
            'current_mood.required' => 'いまの気分を入力してください',
            'current_mood.max' => 'いまの気分は500文字以内で入力してください',
        ];
    }
}
