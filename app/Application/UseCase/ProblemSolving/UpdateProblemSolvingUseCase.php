<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Application\DTO\ProblemSolvingData;
use App\Domain\Entity\ProblemSolving as ProblemSolvingEntity;
use App\Domain\Repository\ProblemSolvingRepositoryInterface;
use DateTimeImmutable;

class UpdateProblemSolvingUseCase
{
    public function __construct(private readonly ProblemSolvingRepositoryInterface $problemSolvingRepository)
    {
    }

    public function handle(int $id, ProblemSolvingData $data): ProblemSolvingEntity
    {
        $existingProblemSolving = $this->problemSolvingRepository->findById($id);

        if ($existingProblemSolving === null) {
            throw new \RuntimeException('ProblemSolving not found');
        }

        $updatedProblemSolving = ProblemSolvingEntity::reconstitute(
            id: $id,
            problemSituation: $data->problemSituation,
            improvedImage: $data->improvedImage,
            actionPlan: $data->actionPlan,
            reflection: $data->reflection,
            solutions: $existingProblemSolving->getSolutions(),
            createdAt: $existingProblemSolving->getCreatedAt(),
            updatedAt: new DateTimeImmutable('now')
        );

        return $this->problemSolvingRepository->save($updatedProblemSolving);
    }
}
