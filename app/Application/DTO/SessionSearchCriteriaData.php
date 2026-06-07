<?php

namespace App\Application\DTO;

readonly class SessionSearchCriteriaData
{
    public const DEFAULT_PER_PAGE = 10;

    public function __construct(
        public ?string $keyword = null,
        public int $page = 1,
        public int $perPage = self::DEFAULT_PER_PAGE,
        public ?string $filter = null
    ) {
    }

    public function hasKeyword(): bool
    {
        return ! empty($this->keyword);
    }
}
