<?php

namespace App\Http\Requests\ProblemSolving;

use App\Application\DTO\PlanSearchCriteriaData;
use Illuminate\Foundation\Http\FormRequest;

class SearchPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'improvement_level_min' => ['nullable', 'integer', 'min:1', 'max:10'],
            'improvement_level_max' => ['nullable', 'integer', 'min:1', 'max:10', 'gte:improvement_level_min'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'keyword.max' => 'キーワードは255文字以内で入力してください。',
            'improvement_level_min.integer' => '改善レベル下限は整数で指定してください。',
            'improvement_level_min.min' => '改善レベル下限は1以上で指定してください。',
            'improvement_level_min.max' => '改善レベル下限は10以下で指定してください。',
            'improvement_level_max.integer' => '改善レベル上限は整数で指定してください。',
            'improvement_level_max.min' => '改善レベル上限は1以上で指定してください。',
            'improvement_level_max.max' => '改善レベル上限は10以下で指定してください。',
            'improvement_level_max.gte' => '改善レベル上限は下限以上で指定してください。',
        ];
    }

    public function toPlanSearchCriteriaData(): PlanSearchCriteriaData
    {
        $validated = $this->validated();

        return new PlanSearchCriteriaData(
            keyword: $validated['keyword'] ?? null,
            improvementLevelMin: (int) ($validated['improvement_level_min'] ?? PlanSearchCriteriaData::DEFAULT_IMPROVEMENT_LEVEL_MIN),
            improvementLevelMax: (int) ($validated['improvement_level_max'] ?? PlanSearchCriteriaData::DEFAULT_IMPROVEMENT_LEVEL_MAX)
        );
    }
}
