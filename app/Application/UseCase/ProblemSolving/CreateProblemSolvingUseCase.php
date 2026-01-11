<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Application\DTO\ProblemSolvingData;
use App\Domain\Entity\ProblemSolving as ProblemSolvingEntity;
use App\Domain\Repository\ProblemSolvingRepositoryInterface;

class CreateProblemSolvingUseCase
{
    public function __construct(private readonly ProblemSolvingRepositoryInterface $problemSolvingRepository)
    {
    }

    public function handle(ProblemSolvingData $data): ProblemSolvingEntity
    {
        $problemSolving = ProblemSolvingEntity::createNew(
            $data->problemSituation,
            $data->improvedImage
        );

        return $this->problemSolvingRepository->save($problemSolving);
    }
}
