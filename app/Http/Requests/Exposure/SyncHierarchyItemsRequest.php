<?php

namespace App\Http\Requests\Exposure;

use Illuminate\Foundation\Http\FormRequest;

class SyncHierarchyItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.content' => ['required', 'string', 'max:500'],
            'items.*.sort_order' => ['required', 'integer', 'min:1'],
            'items.*.expected_suds' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => '不安階層表のデータが必要です',
            'items.*.content.required' => '状況の内容を入力してください',
            'items.*.content.max' => '状況の内容は500文字以内で入力してください',
            'items.*.expected_suds.min' => '不安レベルは0以上で入力してください',
            'items.*.expected_suds.max' => '不安レベルは100以下で入力してください',
        ];
    }
}
