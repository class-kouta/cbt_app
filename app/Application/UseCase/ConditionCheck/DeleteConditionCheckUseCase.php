<?php

namespace App\Application\UseCase\ConditionCheck;

use App\Domain\Repository\ConditionCheckRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class DeleteConditionCheckUseCase
{
    public function __construct(private readonly ConditionCheckRepositoryInterface $conditionCheckRepository)
    {
    }

    public function handle(int $id): void
    {
        $memberId = (int) Auth::id();
        $conditionCheck = $this->conditionCheckRepository->findByIdForMember($id, $memberId);

        if ($conditionCheck === null) {
            throw new DomainException('Condition check not found.');
        }

        $this->conditionCheckRepository->deleteForMember($id, $memberId);
    }
}
