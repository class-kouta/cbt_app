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
            'filter' => ['nullable', 'string', 'in:all,pending,completed'],
        ];
    }

    public function toSessionSearchCriteriaData(): SessionSearchCriteriaData
    {
        $validated = $this->validated();

        return new SessionSearchCriteriaData(
            keyword: $validated['keyword'] ?? null,
            page: (int) ($validated['page'] ?? 1),
            perPage: (int) ($validated['per_page'] ?? SessionSearchCriteriaData::DEFAULT_PER_PAGE),
            filter: $validated['filter'] ?? null
        );
    }
}
