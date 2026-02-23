<?php

namespace App\Http\Requests\SafePlace;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSafePlaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'safe_image' => ['nullable', 'string', 'max:10000'],
            'safe_something' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'safe_image.max' => '安全なイメージは10000文字以下にしてください',
            'safe_something.max' => '安全な何かは10000文字以下にしてください',
        ];
    }
}
