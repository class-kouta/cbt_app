<?php

namespace App\Application\UseCase\AnxietyDiary;

use App\Application\DTO\AnxietyDiaryData;
use App\Domain\Entity\AnxietyDiary as AnxietyDiaryEntity;
use App\Domain\Repository\AnxietyDiaryRepositoryInterface;

class CreateAnxietyDiaryUseCase
{
    public function __construct(private readonly AnxietyDiaryRepositoryInterface $repository)
    {
    }

    public function handle(AnxietyDiaryData $data): AnxietyDiaryEntity
    {
        $anxietyDiary = AnxietyDiaryEntity::createNew(
            $data->situation,
            $data->anxietyThought,
            $data->actualOutcome,
            $data->stressorAndResponseId
        );

        return $this->repository->save($anxietyDiary);
    }
}
