<?php

namespace App\Http\Requests\Exposure;

use App\Rules\SudsScore;

class SyncSessionsRequest extends AddSessionRequest
{
    public function rules(): array
    {
        return [
            'sessions' => ['required', 'array'],
            'sessions.*.id' => ['nullable', 'integer'],
            'sessions.*.hierarchy_item_id' => ['nullable', 'integer'],
            'sessions.*.suds_after' => ['nullable', 'integer', new SudsScore],
            'sessions.*.reflection' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'sessions.required' => '実施記録のデータが必要です',
            'sessions.*.reflection.max' => '振り返りは5000文字以内で入力してください',
        ];
    }
}
