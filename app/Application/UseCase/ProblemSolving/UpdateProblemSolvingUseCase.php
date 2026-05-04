<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Application\DTO\ProblemSolvingData;
use App\Domain\Entity\ProblemSolving as ProblemSolvingEntity;
use App\Domain\Repository\ProblemSolvingRepositoryInterface;
use DateTimeImmutable;
use Illuminate\Support\Facades\Auth;

class UpdateProblemSolvingUseCase
{
    public function __construct(private readonly ProblemSolvingRepositoryInterface $problemSolvingRepository)
    {
    }

    public function handle(int $id, ProblemSolvingData $data): ProblemSolvingEntity
    {
        $existingProblemSolving = $this->problemSolvingRepository->findByIdForMember($id, (int) Auth::id());

        if ($existingProblemSolving === null) {
            throw new \RuntimeException('ProblemSolving not found');
        }

        $updatedProblemSolving = ProblemSolvingEntity::reconstitute(
            id: $id,
            problemSituation: $data->problemSituation,
            improvedImage: $data->improvedImage,
            solutions: $existingProblemSolving->getSolutions(),
            plans: $existingProblemSolving->getPlans(),
            createdAt: $existingProblemSolving->getCreatedAt(),
            updatedAt: new DateTimeImmutable('now')
        );

        return $this->problemSolvingRepository->saveForMember($updatedProblemSolving, (int) Auth::id());
    }
}
