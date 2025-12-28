<?php

namespace App\Http\Requests\SupportNetwork;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupportNetworkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'point' => ['sometimes', 'integer', 'min:0', 'max:99'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'サポート者の名前を入力してください',
            'name.max' => 'サポート者の名前は100文字以内で入力してください',
        ];
    }
}
