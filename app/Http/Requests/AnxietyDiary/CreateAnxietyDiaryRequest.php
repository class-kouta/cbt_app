<?php

namespace App\Http\Requests\AnxietyDiary;

use App\Application\DTO\AnxietyDiaryData;
use Illuminate\Foundation\Http\FormRequest;

class CreateAnxietyDiaryRequest extends FormRequest
{
    use AnxietyDiaryRequestTrait;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * リクエストデータをAnxietyDiaryDataに変換
     */
    public function toAnxietyDiaryData(): AnxietyDiaryData
    {
        return new AnxietyDiaryData(
            situation: (string) $this->string('situation'),
            anxietyThought: $this->filled('anxiety_thought') ? (string) $this->string('anxiety_thought') : null,
            actualOutcome: $this->filled('actual_outcome') ? (string) $this->string('actual_outcome') : null,
            stressorAndResponseId: $this->filled('stressor_and_response_id') ? (int) $this->input('stressor_and_response_id') : null
        );
    }
}
