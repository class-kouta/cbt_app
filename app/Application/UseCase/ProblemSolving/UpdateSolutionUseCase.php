<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Application\DTO\ProblemSolvingSolutionData;
use App\Domain\Entity\ProblemSolvingSolution as ProblemSolvingSolutionEntity;
use App\Domain\Repository\ProblemSolvingRepositoryInterface;
use DateTimeImmutable;

class UpdateSolutionUseCase
{
    public function __construct(private readonly ProblemSolvingRepositoryInterface $problemSolvingRepository)
    {
    }

    public function handle(int $solutionId, ProblemSolvingSolutionData $data): ProblemSolvingSolutionEntity
    {
        $solution = ProblemSolvingSolutionEntity::reconstitute(
            id: $solutionId,
            problemSolvingId: 0, // リポジトリで更新時に使用しない
            content: $data->content,
            effectiveness: $data->effectiveness,
            feasibility: $data->feasibility,
            sortOrder: $data->sortOrder,
            createdAt: new DateTimeImmutable('now'), // リポジトリで更新時に使用しない
            updatedAt: new DateTimeImmutable('now')
        );

        return $this->problemSolvingRepository->updateSolution($solution);
    }
}
