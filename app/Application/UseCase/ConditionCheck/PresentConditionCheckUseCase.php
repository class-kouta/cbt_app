<?php

namespace App\Application\UseCase\ConditionCheck;

use App\Domain\Entity\ConditionCheck as ConditionCheckEntity;
use App\Enums\ConditionCheckRating;

class PresentConditionCheckUseCase
{
    /**
     * @return array<string, mixed>
     */
    public function handle(ConditionCheckEntity $item): array
    {
        $score = ConditionCheckRating::calculateTotalScore(
            $item->getMood(),
            $item->getFatigue(),
            $item->getAnxiety(),
            $item->getSleepiness(),
            $item->getPhysicalCondition(),
        );

        return [
            'id' => $item->getId(),
            'mood' => $item->getMood(),
            'fatigue' => $item->getFatigue(),
            'anxiety' => $item->getAnxiety(),
            'sleepiness' => $item->getSleepiness(),
            'physical_condition' => $item->getPhysicalCondition(),
            'score' => $score,
            'score_class' => $this->scoreTextClassFor($score),
            'max_score' => ConditionCheckRating::maxScore(),
            'memo' => $item->getMemo(),
            'created_at' => $item->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $item->getUpdatedAt()->format(DATE_ATOM),
        ];
    }

    private function scoreTextClassFor(int $score): string
    {
        return match (ConditionCheckRating::scoreStatusFor($score)) {
            'excellent' => 'text-blue-700',
            'good' => 'text-emerald-700',
            'warning' => 'text-orange-700',
            'danger' => 'text-red-700',
            default => 'text-gray-700',
        };
    }
}
