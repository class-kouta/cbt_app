<?php

namespace App\Http\Requests\Mindfulness;

use App\Enums\MindfulnessDuration;
use App\Enums\MindfulnessSound;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAudioUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sound' => ['required', 'string', Rule::in(MindfulnessSound::values())],
            'duration' => ['required', 'integer', Rule::in(MindfulnessDuration::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'sound.required' => '音の種類を選択してください',
            'sound.in' => '無効な音の種類です',
            'duration.required' => '再生時間を選択してください',
            'duration.in' => '無効な再生時間です',
        ];
    }
}
