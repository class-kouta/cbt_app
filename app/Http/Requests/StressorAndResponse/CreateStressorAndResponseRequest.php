<?php

namespace App\Http\Requests\StressorAndResponse;

use Illuminate\Foundation\Http\FormRequest;

class CreateStressorAndResponseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'stressor' => ['required', 'string'],
            'cognition' => ['nullable', 'string'],
            'mood' => ['nullable', 'string'],
            'body_reaction' => ['nullable', 'string'],
            'behavior' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'stressor.required' => 'ストレッサーは必須です。',
        ];
    }
}
