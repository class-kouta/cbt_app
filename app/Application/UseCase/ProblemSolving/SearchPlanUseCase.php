<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Application\DTO\PlanSearchCriteriaData;
use App\Domain\Repository\ProblemSolvingRepositoryInterface;

class SearchPlanUseCase
{
    private const SEARCHABLE_COLUMNS = [
        'action_plan',
        'reflection',
    ];

    public function __construct(
        private readonly ProblemSolvingRepositoryInterface $repository
    ) {
    }

    /**
     * @param PlanSearchCriteriaData $criteria 検索条件
     * @return array<int, array<string, mixed>> 検索結果
     */
    public function handle(PlanSearchCriteriaData $criteria): array
    {
        return $this->repository->searchPlans($criteria, self::SEARCHABLE_COLUMNS);
    }
}
