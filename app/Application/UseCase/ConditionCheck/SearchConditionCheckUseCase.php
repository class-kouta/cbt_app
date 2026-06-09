<?php

namespace App\Application\UseCase\ConditionCheck;

use App\Application\DTO\ConditionCheckSearchCriteriaData;
use App\Domain\Entity\ConditionCheck as ConditionCheckEntity;
use App\Domain\Repository\ConditionCheckRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchConditionCheckUseCase
{
    public function __construct(
        private readonly ConditionCheckRepositoryInterface $conditionCheckRepository,
        private readonly PresentConditionCheckUseCase $presentConditionCheck,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(ConditionCheckSearchCriteriaData $criteria): array
    {
        $result = $this->conditionCheckRepository->searchForMember($criteria, (int) Auth::id());

        $result['data'] = array_map(
            fn (ConditionCheckEntity $entity) => $this->presentConditionCheck->handle($entity),
            $result['data'],
        );

        return $result;
    }
}
