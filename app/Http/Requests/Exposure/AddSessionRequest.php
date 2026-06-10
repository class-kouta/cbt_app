<?php

namespace App\Http\Requests\Exposure;

use Illuminate\Foundation\Http\FormRequest;

class AddSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hierarchy_item_id' => ['required', 'integer'],
            'suds_after' => ['required', 'integer', 'min:0', 'max:100'],
            'reflection' => ['nullable', 'string', 'max:5000'],
            'action_plan' => ['nullable', 'string', 'max:5000'],
            'suds_before' => ['nullable', 'integer', 'min:0', 'max:100'],
            'suds_peak' => ['nullable', 'integer', 'min:0', 'max:100'],
            'performed_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'hierarchy_item_id.required' => '不安階層表を選択してください',
            'suds_after.required' => '実施後の不安レベルを選択してください',
            'suds_after.min' => '実施後の不安レベルは0以上で入力してください',
            'suds_after.max' => '実施後の不安レベルは100以下で入力してください',
            'suds_before.min' => '実施前の不安レベルは0以上で入力してください',
            'suds_before.max' => '実施前の不安レベルは100以下で入力してください',
            'suds_peak.min' => '最高の不安レベルは0以上で入力してください',
            'suds_peak.max' => '最高の不安レベルは100以下で入力してください',
            'performed_at.date' => '正しい日付形式で入力してください',
            'action_plan.max' => '実施計画は5000文字以内で入力してください',
            'reflection.max' => '振り返りは5000文字以内で入力してください',
        ];
    }
}
