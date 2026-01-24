<?php

namespace App\Http\Requests\AnxietyDiary;

use App\Application\DTO\AnxietyDiaryData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAnxietyDiaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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

    /**
     * リクエストデータをAnxietyDiaryDataに変換
     *
     * @param int|null $existingStressorAndResponseId 既存のストレッサーとストレス反応ID（リクエストになければこれを使用）
     */
    public function toAnxietyDiaryData(?int $existingStressorAndResponseId = null): AnxietyDiaryData
    {
        // stressor_and_response_idはリクエストに含まれていなければ既存値を維持
        $stressorAndResponseId = $this->has('stressor_and_response_id')
            ? ($this->filled('stressor_and_response_id') ? (int) $this->input('stressor_and_response_id') : null)
            : $existingStressorAndResponseId;

        return new AnxietyDiaryData(
            situation: (string) $this->string('situation'),
            anxietyThought: $this->filled('anxiety_thought') ? (string) $this->string('anxiety_thought') : null,
            actualOutcome: $this->filled('actual_outcome') ? (string) $this->string('actual_outcome') : null,
            stressorAndResponseId: $stressorAndResponseId
        );
    }
}
