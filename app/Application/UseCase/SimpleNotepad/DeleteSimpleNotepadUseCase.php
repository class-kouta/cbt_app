<?php

namespace App\Application\UseCase\SimpleNotepad;

use App\Domain\Repository\SimpleNotepadRepositoryInterface;

class DeleteSimpleNotepadUseCase
{
    public function __construct(private readonly SimpleNotepadRepositoryInterface $simpleNotepadRepository)
    {
    }

    public function handle(int $id): void
    {
        $this->simpleNotepadRepository->delete($id);
    }
}
