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
        $problemSolving = $this->problemSolvingRepository->findByIdForMember($problemSolvingId, Auth::id());

        if ($problemSolving === null) {
            throw new \RuntimeException('ProblemSolving not found');
        }

        $solution = ProblemSolvingSolutionEntity::createNew(
            $data->content,
            $data->sortOrder,
            $data->effectiveness,
            $data->feasibility
        );

        return $this->problemSolvingRepository->saveSolution($problemSolvingId, $solution);
    }
}
