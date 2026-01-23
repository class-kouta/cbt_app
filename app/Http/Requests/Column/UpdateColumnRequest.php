<?php

namespace App\Http\Requests\Column;

use App\Application\DTO\ColumnData;
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
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
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

    /**
     * リクエストデータをColumnDataに変換
     *
     * @param int|null $existingStressorAndResponseId 既存のストレッサーとストレス反応ID（リクエストになければこれを使用）
     */
    public function toColumnData(?int $existingStressorAndResponseId = null): ColumnData
    {
        // stressor_and_response_idはリクエストに含まれていなければ既存値を維持
        $stressorAndResponseId = $this->has('stressor_and_response_id')
            ? ($this->filled('stressor_and_response_id') ? (int) $this->input('stressor_and_response_id') : null)
            : $existingStressorAndResponseId;

        return new ColumnData(
            situation: (string) $this->string('situation'),
            mood: $this->filled('mood') ? (string) $this->string('mood') : null,
            automaticThought: $this->filled('automatic_thought') ? (string) $this->string('automatic_thought') : null,
            evidence: $this->filled('evidence') ? (string) $this->string('evidence') : null,
            counterEvidence: $this->filled('counter_evidence') ? (string) $this->string('counter_evidence') : null,
            adaptiveThought: $this->filled('adaptive_thought') ? (string) $this->string('adaptive_thought') : null,
            currentMood: $this->filled('current_mood') ? (string) $this->string('current_mood') : null,
            notes: $this->filled('notes') ? (string) $this->string('notes') : null,
            stressorAndResponseId: $stressorAndResponseId,
            tagIds: $this->input('tag_ids', [])
        );
    }
}
