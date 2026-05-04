<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Domain\Repository\ProblemSolvingRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DeletePlanUseCase
{
    public function __construct(private readonly ProblemSolvingRepositoryInterface $problemSolvingRepository)
    {
    }

    public function handle(int $planId): void
    {
        $this->problemSolvingRepository->deletePlanForMember($planId, (int) Auth::id());
    }
}
