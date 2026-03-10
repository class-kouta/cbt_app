<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\HealthyAdultModeImage as HealthyAdultModeImageEntity;
use App\Domain\Repository\HealthyAdultModeImageRepositoryInterface;
use App\Infrastructure\Database\Models\HealthyAdultModeImage as HealthyAdultModeImageModel;
use DateTimeImmutable;

class EloquentHealthyAdultModeImageRepository implements HealthyAdultModeImageRepositoryInterface
{
    public function save(HealthyAdultModeImageEntity $entity): HealthyAdultModeImageEntity
    {
        if ($entity->getId() !== null) {
            $model = HealthyAdultModeImageModel::findOrFail($entity->getId());
        } else {
            $model = new HealthyAdultModeImageModel();
        }

        $model->content = $entity->getContent();
        $model->save();

        return $this->toEntity($model);
    }

    public function findById(int $id): ?HealthyAdultModeImageEntity
    {
        $model = HealthyAdultModeImageModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findFirst(): ?HealthyAdultModeImageEntity
    {
        $model = HealthyAdultModeImageModel::first();

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    private function toEntity(HealthyAdultModeImageModel $model): HealthyAdultModeImageEntity
    {
        return HealthyAdultModeImageEntity::reconstitute(
            id: (int) $model->getKey(),
            content: $model->content,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }
}
