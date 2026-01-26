<?php

namespace App\Application\DTO;

readonly class SearchCriteriaData
{
    /**
     * デフォルトの1ページあたりの表示件数
     */
    public const DEFAULT_PER_PAGE = 10;

    /**
     * @param string|null $keyword キーワード検索文字列
     * @param array<int>|null $tagIds タグIDの配列
     * @param int $page ページ番号（1始まり）
     * @param int $perPage 1ページあたりの表示件数
     */
    public function __construct(
        public ?string $keyword = null,
        public ?array $tagIds = null,
        public int $page = 1,
        public int $perPage = self::DEFAULT_PER_PAGE
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
