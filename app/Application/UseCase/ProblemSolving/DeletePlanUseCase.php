<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Domain\Repository\ProblemSolvingRepositoryInterface;

class DeletePlanUseCase
{
    public function __construct(private readonly ProblemSolvingRepositoryInterface $problemSolvingRepository)
    {
    }

    public function handle(int $planId): void
    {
        $this->problemSolvingRepository->deletePlan($planId);
    }
}
