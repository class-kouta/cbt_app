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

        $existingItems = [];
        foreach ($exposure->getHierarchyItems() as $item) {
            $existingItems[$item->getId()] = $item;
        }

        $items = [];
        foreach ($itemsData as $data) {
            if ($data->id !== null) {
                $existing = $existingItems[$data->id] ?? null;
                if ($existing === null) {
                    throw new \InvalidArgumentException('Invalid hierarchy item');
                }

                $items[] = $existing->update(
                    $data->content,
                    $data->sortOrder,
                    $data->expectedSuds
                );
            } else {
                $items[] = ExposureHierarchyItemEntity::createNew(
                    $data->content,
                    $data->sortOrder,
                    $data->expectedSuds
                );
            }
        }

        return $this->repository->syncHierarchyItemsForMember($exposureId, $items, (int) Auth::id());
    }
}
