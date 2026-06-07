<?php

namespace App\Application\UseCase\Exposure;

use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DeleteSessionUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    public function handle(int $sessionId): void
    {
        $this->repository->deleteSessionForMember($sessionId, (int) Auth::id());
    }
}
