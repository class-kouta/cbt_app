<?php

namespace App\Application\UseCase\AnxietyDiary;

use App\Domain\Repository\AnxietyDiaryRepositoryInterface;

class DeleteAnxietyDiaryUseCase
{
    public function __construct(private readonly AnxietyDiaryRepositoryInterface $repository)
    {
    }

    public function handle(int $id): void
    {
        $this->repository->delete($id);
    }
}
