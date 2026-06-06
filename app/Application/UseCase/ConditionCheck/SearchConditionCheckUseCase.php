<?php

namespace App\Application\UseCase\ConditionCheck;

use App\Application\DTO\ConditionCheckSearchCriteriaData;
use App\Domain\Repository\ConditionCheckRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchConditionCheckUseCase
{
    public function __construct(private readonly ConditionCheckRepositoryInterface $conditionCheckRepository)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(ConditionCheckSearchCriteriaData $criteria): array
    {
        return $this->conditionCheckRepository->searchForMember($criteria, (int) Auth::id());
    }
}
