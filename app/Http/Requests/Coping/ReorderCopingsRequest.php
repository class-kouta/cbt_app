<?php

namespace App\Http\Requests\Coping;

use Illuminate\Foundation\Http\FormRequest;

class ReorderCopingsRequest extends FormRequest
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
            'coping_ids' => ['required', 'array', 'min:1'],
            'coping_ids.*' => ['required', 'integer', 'exists:copings,id'],
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
            'coping_ids.required' => 'コーピングIDリストは必須です',
            'coping_ids.array' => 'コーピングIDリストは配列で指定してください',
            'coping_ids.min' => 'コーピングIDリストには1つ以上のIDが必要です',
            'coping_ids.*.exists' => '指定されたコーピングIDが存在しません',
        ];
    }
}
