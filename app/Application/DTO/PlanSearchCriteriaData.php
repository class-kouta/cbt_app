<?php

namespace App\Application\DTO;

readonly class PlanSearchCriteriaData
{
    public const DEFAULT_IMPROVEMENT_LEVEL_MIN = 1;
    public const DEFAULT_IMPROVEMENT_LEVEL_MAX = 10;
    public const DEFAULT_PER_PAGE = 10;

    /**
     * @param string|null $keyword キーワード検索文字列
     * @param int $improvementLevelMin 改善レベル下限（デフォルト1）
     * @param int $improvementLevelMax 改善レベル上限（デフォルト10）
     * @param int $page ページ番号
     * @param int $perPage 1ページあたりの件数
     * @param string|null $filter フィルター（all/pending/completed）
     */
    public function __construct(
        public ?string $keyword = null,
        public int $improvementLevelMin = self::DEFAULT_IMPROVEMENT_LEVEL_MIN,
        public int $improvementLevelMax = self::DEFAULT_IMPROVEMENT_LEVEL_MAX,
        public int $page = 1,
        public int $perPage = self::DEFAULT_PER_PAGE,
        public ?string $filter = null
    ) {
    }

    public function hasKeyword(): bool
    {
        return !empty($this->keyword);
    }

    public function hasImprovementLevelFilter(): bool
    {
        return $this->improvementLevelMin !== self::DEFAULT_IMPROVEMENT_LEVEL_MIN
            || $this->improvementLevelMax !== self::DEFAULT_IMPROVEMENT_LEVEL_MAX;
    }
}
