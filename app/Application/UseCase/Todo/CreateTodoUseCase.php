<?php

namespace App\Application\UseCase\Todo;

use App\Application\DTO\TodoData;
use App\Domain\Entity\Todo as TodoEntity;
use App\Domain\Repository\TodoRepositoryInterface;

class CreateTodoUseCase
{
    public function __construct(private readonly TodoRepositoryInterface $todoRepository)
    {
    }

    public function handle(TodoData $data): TodoEntity
    {
        $todo = TodoEntity::createNew($data->difficultyId, $data->content, $data->tagIds);
        return $this->todoRepository->save($todo);
    }
}

