<?php

namespace App\Http\Requests\AnxietyDiary;

use App\Application\DTO\AnxietyDiaryData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAnxietyDiaryRequest extends FormRequest
{
    use AnxietyDiaryRequestTrait;

    public function authorize(): bool
    {
        return true;
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
