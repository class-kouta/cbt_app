<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\ExposureHierarchyItemData;
use App\Domain\Entity\ExposureHierarchyItem as ExposureHierarchyItemEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SyncHierarchyItemsUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    /**
     * @param ExposureHierarchyItemData[] $itemsData
     * @return ExposureHierarchyItemEntity[]
     */
    public function handle(int $exposureId, array $itemsData): array
    {
        $exposure = $this->repository->findByIdForMember($exposureId, (int) Auth::id());

        if ($exposure === null) {
            throw new \RuntimeException('Exposure not found');
        }

        $items = array_map(
            fn (ExposureHierarchyItemData $data) => ExposureHierarchyItemEntity::createNew(
                $data->content,
                $data->sortOrder,
                $data->expectedSuds
            ),
            $itemsData
        );

        return $this->repository->syncHierarchyItemsForMember($exposureId, $items, (int) Auth::id());
    }
}
