<?php

namespace App\Application\UseCase\AnxietyDiary;

use App\Application\DTO\AnxietyDiaryData;
use App\Domain\Entity\AnxietyDiary as AnxietyDiaryEntity;
use App\Domain\Repository\AnxietyDiaryRepositoryInterface;

class UpdateAnxietyDiaryUseCase
{
    public function __construct(private readonly AnxietyDiaryRepositoryInterface $repository)
    {
    }

    public function handle(int $id, AnxietyDiaryData $data): AnxietyDiaryEntity
    {
        $existing = $this->repository->findById($id);

        if ($existing === null) {
            throw new \RuntimeException('AnxietyDiary not found');
        }

        $updated = AnxietyDiaryEntity::reconstitute(
            id: $id,
            situation: $data->situation,
            anxietyThought: $data->anxietyThought,
            actualOutcome: $data->actualOutcome,
            stressorAndResponseId: $data->stressorAndResponseId ?? $existing->getStressorAndResponseId(),
            createdAt: $existing->getCreatedAt(),
            updatedAt: new \DateTimeImmutable('now')
        );

        return $this->repository->save($updated);
    }
}
