<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Application\DTO\ProblemSolvingPlanData;
use App\Domain\Entity\ProblemSolvingPlan as ProblemSolvingPlanEntity;
use App\Domain\Repository\ProblemSolvingRepositoryInterface;

class AddPlanUseCase
{
    public function __construct(private readonly ProblemSolvingRepositoryInterface $problemSolvingRepository)
    {
    }

    public function handle(int $problemSolvingId, ProblemSolvingPlanData $data): ProblemSolvingPlanEntity
    {
        $problemSolving = $this->problemSolvingRepository->findById($problemSolvingId);

        if ($problemSolving === null) {
            throw new \RuntimeException('ProblemSolving not found');
        }

        // 次の計画番号を計算
        $latestPlan = $problemSolving->getLatestPlan();
        $nextPlanNumber = $latestPlan ? $latestPlan->getPlanNumber() + 1 : 1;

        $plan = ProblemSolvingPlanEntity::createNew(
            $nextPlanNumber,
            $data->actionPlan,
            $data->reflection,
            $data->improvementLevel
        );

        return $this->problemSolvingRepository->savePlan($problemSolvingId, $plan);
    }
}
