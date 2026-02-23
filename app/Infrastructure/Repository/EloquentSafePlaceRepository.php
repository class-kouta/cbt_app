<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\SafePlace as SafePlaceEntity;
use App\Domain\Repository\SafePlaceRepositoryInterface;
use App\Infrastructure\Database\Models\SafePlace as SafePlaceModel;
use DateTimeImmutable;

class EloquentSafePlaceRepository implements SafePlaceRepositoryInterface
{
    public function save(SafePlaceEntity $safePlace): SafePlaceEntity
    {
        if ($safePlace->getId() !== null) {
            $model = SafePlaceModel::findOrFail($safePlace->getId());
        } else {
            $model = new SafePlaceModel();
        }

        $model->safe_image = $safePlace->getSafeImage();
        $model->safe_something = $safePlace->getSafeSomething();
        $model->save();

        return $this->toEntity($model);
    }

    public function findById(int $id): ?SafePlaceEntity
    {
        $model = SafePlaceModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findFirst(): ?SafePlaceEntity
    {
        $model = SafePlaceModel::first();

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    private function toEntity(SafePlaceModel $model): SafePlaceEntity
    {
        return SafePlaceEntity::reconstitute(
            id: (int) $model->getKey(),
            safeImage: $model->safe_image,
            safeSomething: $model->safe_something,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }
}
