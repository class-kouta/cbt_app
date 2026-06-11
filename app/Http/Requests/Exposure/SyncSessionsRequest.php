<?php

namespace App\Http\Requests\Exposure;

class SyncSessionsRequest extends AddSessionRequest
{
    public function rules(): array
    {
        return [
            'sessions' => ['required', 'array'],
            'sessions.*.id' => ['nullable', 'integer'],
            'sessions.*.hierarchy_item_id' => ['nullable', 'integer'],
            'sessions.*.action_plan' => ['nullable', 'string', 'max:5000'],
            'sessions.*.suds_before' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sessions.*.suds_peak' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sessions.*.suds_after' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sessions.*.performed_at' => ['nullable', 'date'],
            'sessions.*.reflection' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'sessions.required' => '実施記録のデータが必要です',
            'sessions.*.suds_before.min' => '実施前の不安レベルは0以上で入力してください',
            'sessions.*.suds_before.max' => '実施前の不安レベルは100以下で入力してください',
            'sessions.*.suds_peak.min' => '最高の不安レベルは0以上で入力してください',
            'sessions.*.suds_peak.max' => '最高の不安レベルは100以下で入力してください',
            'sessions.*.suds_after.min' => '実施後の不安レベルは0以上で入力してください',
            'sessions.*.suds_after.max' => '実施後の不安レベルは100以下で入力してください',
            'sessions.*.performed_at.date' => '正しい日付形式で入力してください',
            'sessions.*.action_plan.max' => '実施計画は5000文字以内で入力してください',
            'sessions.*.reflection.max' => '振り返りは5000文字以内で入力してください',
        ];
    }
}
