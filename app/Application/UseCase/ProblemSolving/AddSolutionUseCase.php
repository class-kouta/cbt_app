<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Application\DTO\ProblemSolvingSolutionData;
use App\Domain\Entity\ProblemSolvingSolution as ProblemSolvingSolutionEntity;
use App\Domain\Repository\ProblemSolvingRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AddSolutionUseCase
{
    public function __construct(private readonly ProblemSolvingRepositoryInterface $problemSolvingRepository)
    {
    }

    public function handle(int $problemSolvingId, ProblemSolvingSolutionData $data): ProblemSolvingSolutionEntity
    {
        $memberId = (int) Auth::id();
        $problemSolving = $this->problemSolvingRepository->findByIdForMember($problemSolvingId, $memberId);

        if ($problemSolving === null) {
            throw new \RuntimeException('ProblemSolving not found');
        }

        $solution = ProblemSolvingSolutionEntity::createNew(
            $data->content,
            $data->sortOrder,
            $data->effectiveness,
            $data->feasibility
        );

        return $this->problemSolvingRepository->saveSolutionForMember($problemSolvingId, $solution, $memberId);
    }
}
