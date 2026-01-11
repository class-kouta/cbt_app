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

        if (!$problemSolving->canAddNewPlan()) {
            throw new \RuntimeException('最新の計画の振り返りを入力してから、新しい計画を追加してください。');
        }

        // 次の計画番号を計算
        $latestPlan = $problemSolving->getLatestPlan();
        $nextPlanNumber = $latestPlan ? $latestPlan->getPlanNumber() + 1 : 1;

        $plan = ProblemSolvingPlanEntity::createNew(
            $nextPlanNumber,
            $data->actionPlan,
            $data->reflection
        );

        return $this->problemSolvingRepository->savePlan($problemSolvingId, $plan);
    }
}
