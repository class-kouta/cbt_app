<?php

namespace App\Http\Requests\ModeMap;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModeMapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'wounded_child_mode' => ['nullable', 'string', 'max:10000'],
            'hurtful_adult_mode' => ['nullable', 'string', 'max:10000'],
            'unacceptable_coping_mode' => ['nullable', 'string', 'max:10000'],
            'healthy_happy_child_mode' => ['nullable', 'string', 'max:10000'],
            'healthy_adult_mode' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'wounded_child_mode.max' => '傷ついた子どもモードは10000文字以下にしてください',
            'hurtful_adult_mode.max' => '傷つける大人モードは10000文字以下にしてください',
            'unacceptable_coping_mode.max' => 'いただけない対処モードは10000文字以下にしてください',
            'healthy_happy_child_mode.max' => 'ヘルシーモード(幸せな子どもモード)は10000文字以下にしてください',
            'healthy_adult_mode.max' => 'ヘルシーモード(ヘルシーな大人モード)は10000文字以下にしてください',
        ];
    }
}
