<?php

namespace App\Application\UseCase\Coping;

use App\Domain\Repository\CopingRepositoryInterface;

class DeleteCopingUseCase
{
    public function __construct(private readonly CopingRepositoryInterface $copingRepository)
    {
    }

    public function handle(int $id): void
    {
        $this->copingRepository->delete($id);
    }
}
