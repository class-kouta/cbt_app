<?php

namespace App\Http\Requests\Exposure;

use App\Rules\SudsScore;
use Illuminate\Foundation\Http\FormRequest;

class CreateExposureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'avoidance_target' => ['required', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'avoidance_target.required' => '回避していることを入力してください',
            'avoidance_target.max' => '回避していることは5000文字以内で入力してください',
            'notes.max' => '備考は5000文字以内で入力してください',
        ];
    }
}
