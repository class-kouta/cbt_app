<?php

namespace App\Application\UseCase\Exposure;

use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DeleteExposureUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    public function handle(int $id): void
    {
        $this->repository->deleteForMember($id, (int) Auth::id());
    }
}
