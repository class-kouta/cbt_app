<?php

namespace App\Http\Requests\Exposure;

use App\Application\DTO\SessionSearchCriteriaData;
use Illuminate\Foundation\Http\FormRequest;

class SearchSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'exposure_id' => ['nullable', 'integer'],
            'hierarchy_item_id' => ['nullable', 'integer', 'required_with:exposure_id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('hierarchy_item_id') && ! $this->filled('exposure_id')) {
                $validator->errors()->add('hierarchy_item_id', '回避していることを先に選択してください');
            }
        });
    }

    public function toSessionSearchCriteriaData(): SessionSearchCriteriaData
    {
        $validated = $this->validated();

        return new SessionSearchCriteriaData(
            keyword: $validated['keyword'] ?? null,
            page: (int) ($validated['page'] ?? 1),
            perPage: (int) ($validated['per_page'] ?? SessionSearchCriteriaData::DEFAULT_PER_PAGE),
            exposureId: isset($validated['exposure_id']) ? (int) $validated['exposure_id'] : null,
            hierarchyItemId: isset($validated['hierarchy_item_id']) ? (int) $validated['hierarchy_item_id'] : null
        );
    }
}
