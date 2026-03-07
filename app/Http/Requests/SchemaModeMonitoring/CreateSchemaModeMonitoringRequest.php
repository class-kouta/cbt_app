<?php

namespace App\Http\Requests\SchemaModeMonitoring;

use Illuminate\Foundation\Http\FormRequest;

class CreateSchemaModeMonitoringRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'モニタリング内容を入力してください',
            'content.max' => 'モニタリング内容は10000文字以内で入力してください',
        ];
    }
}
