<?php

namespace App\Application\UseCase\ConditionCheck;

use App\Application\DTO\ConditionCheckData;
use App\Domain\Entity\ConditionCheck as ConditionCheckEntity;
use App\Domain\Repository\ConditionCheckRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateConditionCheckUseCase
{
    public function __construct(private readonly ConditionCheckRepositoryInterface $conditionCheckRepository)
    {
    }

    public function handle(ConditionCheckData $data): ConditionCheckEntity
    {
        $conditionCheck = ConditionCheckEntity::createNew(
            mood: $data->mood,
            fatigue: $data->fatigue,
            anxiety: $data->anxiety,
            sleepiness: $data->sleepiness,
            physicalCondition: $data->physicalCondition,
            memo: $data->memo,
        );

        return $this->conditionCheckRepository->saveForMember($conditionCheck, (int) Auth::id());
    }
}
