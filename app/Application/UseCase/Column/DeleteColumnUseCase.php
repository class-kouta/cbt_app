<?php

namespace App\Application\UseCase\Column;

use App\Domain\Repository\ColumnRepositoryInterface;

class DeleteColumnUseCase
{
    public function __construct(private readonly ColumnRepositoryInterface $columnRepository)
    {
    }

    public function handle(int $id): void
    {
        $this->columnRepository->delete($id);
    }
}
