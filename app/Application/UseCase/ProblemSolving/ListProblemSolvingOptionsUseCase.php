<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Domain\Repository\ProblemSolvingRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ListProblemSolvingOptionsUseCase
{
    public function __construct(
        private readonly ProblemSolvingRepositoryInterface $repository
    ) {
    }

    /**
     * @return array<int, array{id: int, problem_situation: string}>
     */
    public function handle(): array
    {
        return $this->repository->listOptionsForMember((int) Auth::id());
    }
}
