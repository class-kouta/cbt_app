<?php

namespace App\Http\Requests\ConditionCheck;

use App\Application\DTO\ConditionCheckSearchCriteriaData;
use Illuminate\Foundation\Http\FormRequest;

class ConditionCheckIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'page.integer' => 'ページ番号は整数で指定してください。',
            'page.min' => 'ページ番号は1以上で指定してください。',
            'per_page.integer' => '表示件数は整数で指定してください。',
            'per_page.min' => '表示件数は1以上で指定してください。',
            'per_page.max' => '表示件数は100以下で指定してください。',
        ];
    }

    public function toSearchCriteriaData(): ConditionCheckSearchCriteriaData
    {
        $validated = $this->validated();

        return new ConditionCheckSearchCriteriaData(
            page: (int) ($validated['page'] ?? 1),
            perPage: (int) ($validated['per_page'] ?? ConditionCheckSearchCriteriaData::DEFAULT_PER_PAGE),
        );
    }
}
