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
        return array_merge(parent::messages(), [
            'sessions.required' => '実施記録のデータが必要です',
        ]);
    }
}
