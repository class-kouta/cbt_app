<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\ExposureHierarchyItemData;
use App\Domain\Entity\ExposureHierarchyItem as ExposureHierarchyItemEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdateHierarchyItemUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    public function handle(int $itemId, ExposureHierarchyItemData $data): ExposureHierarchyItemEntity
    {
        $memberId = (int) Auth::id();
        $existing = $this->repository->findHierarchyItemByIdForMember($itemId, $memberId);

        if ($existing === null) {
            throw new \RuntimeException('Hierarchy item not found');
        }

        $updated = $existing->update(
            $data->content,
            $data->sortOrder,
            $data->expectedSuds
        );

        return $this->repository->updateHierarchyItemForMember($updated, $memberId);
    }
}
