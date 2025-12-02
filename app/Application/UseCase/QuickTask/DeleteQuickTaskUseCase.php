<?php

namespace App\Application\UseCase\QuickTask;

use App\Domain\Repository\QuickTaskRepositoryInterface;

class DeleteQuickTaskUseCase
{
    public function __construct(private readonly QuickTaskRepositoryInterface $quickTaskRepository)
    {
    }

    public function handle(int $id): void
    {
        $this->quickTaskRepository->delete($id);
    }
}
