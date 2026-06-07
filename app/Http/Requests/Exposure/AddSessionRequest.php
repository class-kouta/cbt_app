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
            'hierarchy_item_id' => ['nullable', 'integer'],
            'action_plan' => ['nullable', 'string', 'max:5000'],
            'suds_before' => ['nullable', 'integer', 'min:0', 'max:100'],
            'suds_peak' => ['nullable', 'integer', 'min:0', 'max:100'],
            'suds_after' => ['nullable', 'integer', 'min:0', 'max:100'],
            'performed_at' => ['nullable', 'date'],
            'reflection' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
