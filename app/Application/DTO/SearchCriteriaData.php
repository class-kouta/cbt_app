<?php

namespace App\Application\DTO;

readonly class SearchCriteriaData
{
    /**
     * @param string|null $keyword キーワード検索文字列
     * @param array<int>|null $tagIds タグIDの配列
     */
    public function __construct(
        public ?string $keyword = null,
        public ?array $tagIds = null
    ) {
    }

    /**
     * 検索条件が空かどうか
     */
    public function isEmpty(): bool
    {
        return empty($this->keyword) && empty($this->tagIds);
    }

    /**
     * キーワードが設定されているか
     */
    public function hasKeyword(): bool
    {
        return !empty($this->keyword);
    }

    /**
     * タグIDが設定されているか
     */
    public function hasTagIds(): bool
    {
        return !empty($this->tagIds);
    }
}
