<?php

namespace App\Application\UseCase\ConditionCheck;

use App\Application\DTO\ConditionCheckData;
use App\Domain\Entity\ConditionCheck as ConditionCheckEntity;
use App\Domain\Repository\ConditionCheckRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class UpdateConditionCheckUseCase
{
    public function __construct(private readonly ConditionCheckRepositoryInterface $conditionCheckRepository)
    {
    }

    public function handle(int $id, ConditionCheckData $data): ConditionCheckEntity
    {
        $memberId = (int) Auth::id();
        $conditionCheck = $this->conditionCheckRepository->findByIdForMember($id, $memberId);

        if ($conditionCheck === null) {
            throw new DomainException('Condition check not found.');
        }

        $updated = $conditionCheck->update(
            mood: $data->mood,
            fatigue: $data->fatigue,
            anxiety: $data->anxiety,
            sleepiness: $data->sleepiness,
            physicalCondition: $data->physicalCondition,
            memo: $data->memo,
        );

        return $this->conditionCheckRepository->saveForMember($updated, $memberId);
    }
}
