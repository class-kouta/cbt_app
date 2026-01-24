<?php

namespace App\Http\Requests\AnxietyDiary;

trait AnxietyDiaryRequestTrait
{
    public function rules(): array
    {
        return [
            'situation' => ['required', 'string', 'max:1000'],
            'anxiety_thought' => ['nullable', 'string', 'max:1000'],
            'actual_outcome' => ['nullable', 'string', 'max:1000'],
            'stressor_and_response_id' => ['nullable', 'integer', 'exists:stressor_and_responses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'situation.required' => '状況を入力してください',
            'situation.max' => '状況は1000文字以内で入力してください',
            'anxiety_thought.max' => '不安な考えは1000文字以内で入力してください',
            'actual_outcome.max' => '実際にどうなったかは1000文字以内で入力してください',
        ];
    }
}
