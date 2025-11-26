<?php

namespace App\Http\Requests\Coping;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCopingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:200'],
            'coping_tag_ids' => ['sometimes', 'array'],
            'coping_tag_ids.*' => ['integer', 'min:1', 'distinct', 'exists:coping_tags,id'],
            'point' => ['sometimes', 'integer', 'min:0', 'max:99'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'コーピング内容を入力してください',
            'content.max' => 'コーピング内容は200文字以内で入力してください',
            'coping_tag_ids.*.exists' => '選択されたタグは存在しません',
        ];
    }
}
