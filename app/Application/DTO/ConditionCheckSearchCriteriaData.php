<?php

namespace App\Application\DTO;

readonly class ConditionCheckSearchCriteriaData
{
    public const DEFAULT_PER_PAGE = 30;

    public function __construct(
        public int $page = 1,
        public int $perPage = self::DEFAULT_PER_PAGE,
    ) {
    }
}
