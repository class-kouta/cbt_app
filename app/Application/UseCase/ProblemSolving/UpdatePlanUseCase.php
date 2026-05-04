<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Application\DTO\ProblemSolvingPlanData;
use App\Domain\Entity\ProblemSolvingPlan as ProblemSolvingPlanEntity;
use App\Domain\Repository\ProblemSolvingRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdatePlanUseCase
{
    public function __construct(private readonly ProblemSolvingRepositoryInterface $problemSolvingRepository)
    {
    }

    public function handle(int $planId, ProblemSolvingPlanData $data): ProblemSolvingPlanEntity
    {
        $existingPlan = $this->problemSolvingRepository->findPlanByIdForMember($planId, (int) Auth::id());

        if ($existingPlan === null) {
            throw new \RuntimeException('Plan not found');
        }

        $updatedPlan = $existingPlan->update(
            $data->actionPlan,
            $data->reflection,
            $data->improvementLevel
        );

        return $this->problemSolvingRepository->updatePlanForMember($updatedPlan, (int) Auth::id());
    }
}
