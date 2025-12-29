<?php

namespace App\Application\UseCase\StressorAndResponse;

use App\Domain\Repository\StressorAndResponseRepositoryInterface;

class DeleteStressorAndResponseUseCase
{
    public function __construct(private readonly StressorAndResponseRepositoryInterface $repository)
    {
    }

    public function handle(int $id): void
    {
        $this->repository->delete($id);
    }
}
