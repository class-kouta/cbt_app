<?php

namespace App\Application\DTO;

readonly class SessionSearchCriteriaData
{
    public const DEFAULT_PER_PAGE = 10;

    public function __construct(
        public ?string $keyword = null,
        public int $page = 1,
        public int $perPage = self::DEFAULT_PER_PAGE,
        public ?int $exposureId = null,
        public ?int $hierarchyItemId = null
    ) {
    }

    public function hasKeyword(): bool
    {
        return ! empty($this->keyword);
    }

    public function hasExposureId(): bool
    {
        return $this->exposureId !== null;
    }

    public function hasHierarchyItemId(): bool
    {
        return $this->hierarchyItemId !== null;
    }
}
