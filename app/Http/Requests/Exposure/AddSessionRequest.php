<?php

namespace App\Http\Requests\Exposure;

use App\Rules\SudsScore;
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
            'suds_after' => ['required', 'integer', new SudsScore],
            'reflection' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'hierarchy_item_id.required' => '不安階層表を選択してください',
            'suds_after.required' => '実施後の不安レベルを選択してください',
            'reflection.max' => '振り返りは5000文字以内で入力してください',
        ];
    }
}
