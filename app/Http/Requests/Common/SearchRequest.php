<?php

namespace App\Http\Requests\Common;

use App\Application\DTO\SearchCriteriaData;
use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'keyword.max' => 'キーワードは255文字以内で入力してください。',
            'tag_ids.array' => 'タグIDは配列形式で指定してください。',
            'tag_ids.*.integer' => 'タグIDは整数で指定してください。',
            'tag_ids.*.exists' => '指定されたタグIDは存在しません。',
        ];
    }

    /**
     * バリデート済みデータから検索条件DTOを生成
     */
    public function toSearchCriteriaData(): SearchCriteriaData
    {
        $validated = $this->validated();

        return new SearchCriteriaData(
            keyword: $validated['keyword'] ?? null,
            tagIds: $validated['tag_ids'] ?? null
        );
    }
}
