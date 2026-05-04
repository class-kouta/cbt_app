<?php

namespace App\Application\UseCase\Column;

use App\Domain\Repository\ColumnRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DeleteColumnUseCase
{
    public function __construct(private readonly ColumnRepositoryInterface $columnRepository)
    {
    }

    public function handle(int $id): void
    {
        $this->columnRepository->deleteForMember($id, (int) Auth::id());
    }
}
