<?php

namespace App\Http\Requests\Mindfulness;

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
            'sound' => ['required', 'string', Rule::in(['forest', 'stream', 'jungle'])],
            'duration' => ['required', 'integer', Rule::in([5, 10, 15])],
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
