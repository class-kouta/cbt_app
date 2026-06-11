<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\ExposureHierarchyItemData;
use App\Domain\Entity\ExposureHierarchyItem as ExposureHierarchyItemEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AddHierarchyItemUseCase
{
    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    public function handle(int $exposureId, ExposureHierarchyItemData $data): ExposureHierarchyItemEntity
    {
        $exposure = $this->repository->findByIdForMember($exposureId, (int) Auth::id());

        if ($exposure === null) {
            throw new \RuntimeException('Exposure not found');
        }

        $item = ExposureHierarchyItemEntity::createNew(
            $data->content,
            $data->sortOrder,
            $data->expectedSuds
        );

        return $this->repository->saveHierarchyItemForMember($exposureId, $item, (int) Auth::id());
    }
}
