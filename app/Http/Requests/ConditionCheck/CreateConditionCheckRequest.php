<?php

namespace App\Http\Requests\ConditionCheck;

use App\Enums\ConditionCheckRating;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateConditionCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->sharedRules();
    }

    public function messages(): array
    {
        return $this->sharedMessages();
    }

    /**
     * @return array<string, mixed>
     */
    protected function sharedRules(): array
    {
        $ratingRule = ['required', Rule::enum(ConditionCheckRating::class)];

        return [
            'mood' => $ratingRule,
            'fatigue' => $ratingRule,
            'anxiety' => $ratingRule,
            'sleepiness' => $ratingRule,
            'physical_condition' => $ratingRule,
            'memo' => ['nullable', 'string', 'max:10000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function sharedMessages(): array
    {
        return [
            'mood.required' => '気分を選択してください',
            'fatigue.required' => '疲労感を選択してください',
            'anxiety.required' => '不安を選択してください',
            'sleepiness.required' => '眠気を選択してください',
            'physical_condition.required' => '体の調子を選択してください',
            'memo.max' => 'メモは10000文字以内で入力してください',
        ];
    }
}
