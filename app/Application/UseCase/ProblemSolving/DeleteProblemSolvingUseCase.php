<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Domain\Repository\ProblemSolvingRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DeleteProblemSolvingUseCase
{
    public function __construct(private readonly ProblemSolvingRepositoryInterface $problemSolvingRepository)
    {
    }

    public function handle(int $id): void
    {
        $this->problemSolvingRepository->deleteForMember($id, (int) Auth::id());
    }
}
