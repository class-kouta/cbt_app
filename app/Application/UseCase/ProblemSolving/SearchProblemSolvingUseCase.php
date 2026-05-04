<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Repository\ProblemSolvingRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchProblemSolvingUseCase
{
    /**
     * キーワード検索対象カラム
     */
    private const SEARCHABLE_COLUMNS = [
        'problem_situation',
        'improved_image',
    ];

    public function __construct(
        private readonly ProblemSolvingRepositoryInterface $repository
    ) {
    }

    /**
     * 検索条件に基づいて問題解決法を検索
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @return array<int, array<string, mixed>> 検索結果
     */
    public function handle(SearchCriteriaData $criteria): array
    {
        return $this->repository->searchForMember($criteria, self::SEARCHABLE_COLUMNS, Auth::id());
    }
}
