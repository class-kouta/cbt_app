<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\ExposureHierarchyItemData;
use App\Domain\Entity\ExposureHierarchyItem as ExposureHierarchyItemEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use DateTimeImmutable;
use Illuminate\Support\Facades\Auth;

class UpdateHierarchyItemUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    public function handle(int $itemId, ExposureHierarchyItemData $data): ExposureHierarchyItemEntity
    {
        $item = ExposureHierarchyItemEntity::reconstitute(
            id: $itemId,
            exposureId: 0,
            content: $data->content,
            expectedSuds: $data->expectedSuds,
            sortOrder: $data->sortOrder,
            createdAt: new DateTimeImmutable('now'),
            updatedAt: new DateTimeImmutable('now')
        );

        return $this->repository->updateHierarchyItemForMember($item, (int) Auth::id());
    }
}
