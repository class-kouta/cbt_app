<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * 検索機能の共通ロジックを提供するトレイト
 */
trait Searchable
{
    /**
     * 検索パラメータのバリデーションルール
     *
     * @return array<string, string>
     */
    protected function getSearchValidationRules(): array
    {
        return [
            'keyword' => 'nullable|string|max:255',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:tags,id',
        ];
    }

    /**
     * 検索パラメータをバリデートして取得
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    protected function validateSearchParams(Request $request): array
    {
        return $request->validate($this->getSearchValidationRules());
    }

    /**
     * キーワード検索条件をクエリに適用
     *
     * @param Builder $query
     * @param array<string, mixed> $validated バリデート済みパラメータ
     * @param array<int, string> $searchableColumns 検索対象カラム名の配列
     * @return Builder
     */
    protected function applyKeywordSearch(Builder $query, array $validated, array $searchableColumns): Builder
    {
        if (!empty($validated['keyword']) && count($searchableColumns) > 0) {
            $keyword = $validated['keyword'];
            $query->where(function ($q) use ($keyword, $searchableColumns) {
                foreach ($searchableColumns as $index => $column) {
                    if ($index === 0) {
                        $q->where($column, 'like', "%{$keyword}%");
                    } else {
                        $q->orWhere($column, 'like', "%{$keyword}%");
                    }
                }
            });
        }

        return $query;
    }

    /**
     * タグ検索条件をクエリに適用
     *
     * @param Builder $query
     * @param array<string, mixed> $validated バリデート済みパラメータ
     * @return Builder
     */
    protected function applyTagSearch(Builder $query, array $validated): Builder
    {
        if (!empty($validated['tag_ids'])) {
            $query->whereHas('tags', function ($q) use ($validated) {
                $q->whereIn('tags.id', $validated['tag_ids']);
            });
        }

        return $query;
    }

    /**
     * 検索条件を適用する共通メソッド
     *
     * @param Builder $query
     * @param Request $request
     * @param array<int, string> $searchableColumns 検索対象カラム名の配列
     * @return Builder
     */
    protected function applySearchFilters(Builder $query, Request $request, array $searchableColumns): Builder
    {
        $validated = $this->validateSearchParams($request);

        $this->applyKeywordSearch($query, $validated, $searchableColumns);
        $this->applyTagSearch($query, $validated);

        return $query;
    }
}
