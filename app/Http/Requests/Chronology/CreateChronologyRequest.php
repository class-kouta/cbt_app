<?php

namespace App\Http\Requests\Chronology;

use App\Domain\Entity\Chronology;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateChronologyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'when_period' => ['required', 'string', 'max:200'],
            'environment_event' => ['nullable', 'string', 'max:10000'],
            'experience_feeling' => ['nullable', 'string', 'max:10000'],
            'sentiment_type' => ['nullable', 'string', Rule::in(Chronology::VALID_SENTIMENTS)],
        ];
    }

    public function messages(): array
    {
        return [
            'when_period.required' => '「いつ」を入力してください',
            'when_period.max' => '「いつ」は200文字以内で入力してください',
            'environment_event.max' => '「環境・出来事」は10000文字以内で入力してください',
            'experience_feeling.max' => '「体験・感じたこと・思ったこと」は10000文字以内で入力してください',
            'sentiment_type.in' => 'タグはポジティブまたはネガティブを選択してください',
        ];
    }
}
