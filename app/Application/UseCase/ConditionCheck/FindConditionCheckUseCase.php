<?php

namespace App\Application\UseCase\ConditionCheck;

use App\Domain\Entity\ConditionCheck as ConditionCheckEntity;
use App\Domain\Repository\ConditionCheckRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class FindConditionCheckUseCase
{
    public function __construct(private readonly ConditionCheckRepositoryInterface $conditionCheckRepository)
    {
    }

    public function handle(int $id): ConditionCheckEntity
    {
        $conditionCheck = $this->conditionCheckRepository->findByIdForMember($id, (int) Auth::id());

        if ($conditionCheck === null) {
            throw new DomainException('Condition check not found.');
        }

        return $conditionCheck;
    }
}
