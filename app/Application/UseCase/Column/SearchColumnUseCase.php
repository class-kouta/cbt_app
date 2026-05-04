<?php

namespace App\Application\UseCase\Column;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Repository\ColumnRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchColumnUseCase
{
    /**
     * キーワード検索対象カラム
     */
    private const SEARCHABLE_COLUMNS = [
        'situation',
        'mood',
        'automatic_thought',
        'evidence',
        'counter_evidence',
        'adaptive_thought',
        'current_mood',
        'notes',
    ];

    public function __construct(
        private readonly ColumnRepositoryInterface $repository
    ) {
    }

    /**
     * 検索条件に基づいてコラムを検索
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @return array<int, array<string, mixed>> 検索結果
     */
    public function handle(SearchCriteriaData $criteria): array
    {
        return $this->repository->searchForMember($criteria, self::SEARCHABLE_COLUMNS, (int) Auth::id());
    }
}
