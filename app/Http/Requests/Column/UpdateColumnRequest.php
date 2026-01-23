<?php

namespace App\Http\Requests\Column;

use Illuminate\Foundation\Http\FormRequest;

class UpdateColumnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'situation' => ['required', 'string', 'max:1000'],
            'mood' => ['nullable', 'string', 'max:500'],
            'automatic_thought' => ['nullable', 'string', 'max:1000'],
            'evidence' => ['nullable', 'string', 'max:1000'],
            'counter_evidence' => ['nullable', 'string', 'max:1000'],
            'adaptive_thought' => ['nullable', 'string', 'max:1000'],
            'current_mood' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'stressor_and_response_id' => ['nullable', 'integer', 'exists:stressor_and_responses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'situation.required' => '状況を入力してください',
            'situation.max' => '状況は1000文字以内で入力してください',
            'mood.max' => '気分は500文字以内で入力してください',
            'automatic_thought.max' => '自動思考は1000文字以内で入力してください',
            'evidence.max' => '根拠は1000文字以内で入力してください',
            'counter_evidence.max' => '反証は1000文字以内で入力してください',
            'adaptive_thought.max' => '適応的思考は1000文字以内で入力してください',
            'current_mood.max' => 'いまの気分は500文字以内で入力してください',
            'notes.max' => '備考は2000文字以内で入力してください',
        ];
    }
}
