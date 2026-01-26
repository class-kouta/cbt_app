<?php

namespace App\Application\UseCase\AnxietyDiary;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Repository\AnxietyDiaryRepositoryInterface;

class SearchAnxietyDiaryUseCase
{
    /**
     * キーワード検索対象カラム
     */
    private const SEARCHABLE_COLUMNS = [
        'situation',
        'anxiety_thought',
        'actual_outcome',
    ];

    public function __construct(
        private readonly AnxietyDiaryRepositoryInterface $repository
    ) {
    }

    /**
     * 検索条件に基づいて不安日記を検索（ページネーション対応）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @return array<string, mixed> 検索結果（ページネーション情報を含む）
     */
    public function handle(SearchCriteriaData $criteria): array
    {
        return $this->repository->search($criteria, self::SEARCHABLE_COLUMNS);
    }
}
