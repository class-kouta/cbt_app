<?php

namespace App\Http\Requests\Coping;

use Illuminate\Foundation\Http\FormRequest;

class ReorderCopingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['integer', 'min:1', 'distinct', 'exists:copings,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ordered_ids.required' => '並び順のIDリストが必要です',
            'ordered_ids.array' => '並び順のIDリストは配列である必要があります',
            'ordered_ids.*.integer' => 'IDは整数である必要があります',
            'ordered_ids.*.exists' => '指定されたコーピングは存在しません',
            'ordered_ids.*.distinct' => 'IDは重複できません',
        ];
    }
}
