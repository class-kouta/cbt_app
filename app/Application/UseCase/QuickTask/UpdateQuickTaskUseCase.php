<?php

namespace App\Application\UseCase\QuickTask;

use App\Application\DTO\QuickTaskData;
use App\Domain\Entity\QuickTask as QuickTaskEntity;
use App\Domain\Repository\QuickTaskRepositoryInterface;
use DomainException;

class UpdateQuickTaskUseCase
{
    public function __construct(private readonly QuickTaskRepositoryInterface $quickTaskRepository)
    {
    }

    public function handle(int $id, QuickTaskData $data): QuickTaskEntity
    {
        $quickTask = $this->quickTaskRepository->findById($id);

        if ($quickTask === null) {
            throw new DomainException('Quick task not found.');
        }

        $updatedQuickTask = $quickTask->updateContent($data->content, $data->difficultyId, $data->tagIds);
        return $this->quickTaskRepository->save($updatedQuickTask);
    }
}
