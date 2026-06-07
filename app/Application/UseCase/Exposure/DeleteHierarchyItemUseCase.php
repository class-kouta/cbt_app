<?php

namespace App\Application\UseCase\Exposure;

use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DeleteHierarchyItemUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    public function handle(int $itemId): void
    {
        $this->repository->deleteHierarchyItemForMember($itemId, (int) Auth::id());
    }
}
