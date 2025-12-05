<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Domain\Repository\ProblemSolvingRepositoryInterface;

class DeleteSolutionUseCase
{
    public function __construct(private readonly ProblemSolvingRepositoryInterface $problemSolvingRepository)
    {
    }

    public function handle(int $solutionId): void
    {
        $this->problemSolvingRepository->deleteSolution($solutionId);
    }
}
