<?php

namespace App\Application\UseCase\Todo;

use App\Domain\Repository\TodoRepositoryInterface;

class UncompleteTodoUseCase
{
    private TodoRepositoryInterface $todoRepository;

    public function __construct(TodoRepositoryInterface $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }

    public function handle(int $todoId): void
    {
        $this->todoRepository->uncomplete($todoId);
    }
}
