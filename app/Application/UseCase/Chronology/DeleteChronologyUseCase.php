<?php

namespace App\Application\UseCase\Chronology;

use App\Domain\Repository\ChronologyRepositoryInterface;

class DeleteChronologyUseCase
{
    public function __construct(private readonly ChronologyRepositoryInterface $chronologyRepository) {}

    public function handle(int $id): void
    {
        $this->chronologyRepository->delete($id);
    }
}
