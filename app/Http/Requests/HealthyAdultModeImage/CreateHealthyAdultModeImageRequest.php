<?php

namespace App\Http\Requests\HealthyAdultModeImage;

use Illuminate\Foundation\Http\FormRequest;

class CreateHealthyAdultModeImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.max' => 'ヘルシーな大人モードのイメージは10000文字以下にしてください',
        ];
    }
}
