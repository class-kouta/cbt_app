<?php

namespace App\Application\UseCase\QuickTask;

use App\Application\DTO\QuickTaskData;
use App\Domain\Entity\QuickTask as QuickTaskEntity;
use App\Domain\Repository\QuickTaskRepositoryInterface;

class CreateQuickTaskUseCase
{
    public function __construct(private readonly QuickTaskRepositoryInterface $quickTaskRepository)
    {
    }

    public function handle(QuickTaskData $data): QuickTaskEntity
    {
        $quickTask = QuickTaskEntity::createNew($data->content, $data->difficultyId, $data->tagIds);
        return $this->quickTaskRepository->save($quickTask);
    }
}
