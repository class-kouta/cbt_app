<?php

namespace App\Application\UseCase\StressorAndResponse;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Repository\StressorAndResponseRepositoryInterface;

class SearchStressorAndResponseUseCase
{
    /**
     * キーワード検索対象カラム
     */
    private const SEARCHABLE_COLUMNS = [
        'stressor',
        'cognition',
        'mood',
        'body_reaction',
        'behavior',
    ];

    public function __construct(
        private readonly StressorAndResponseRepositoryInterface $repository
    ) {
    }

    /**
     * 検索条件に基づいてストレッサーとストレス反応を検索
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @return array<int, array<string, mixed>> 検索結果
     */
    public function handle(SearchCriteriaData $criteria): array
    {
        return $this->repository->search($criteria, self::SEARCHABLE_COLUMNS);
    }
}
