<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\ModeMap as ModeMapEntity;
use App\Domain\Repository\ModeMapRepositoryInterface;
use App\Infrastructure\Database\Models\ModeMap as ModeMapModel;
use DateTimeImmutable;

class EloquentModeMapRepository implements ModeMapRepositoryInterface
{
    public function save(ModeMapEntity $modeMap): ModeMapEntity
    {
        if ($modeMap->getId() !== null) {
            $model = ModeMapModel::findOrFail($modeMap->getId());
        } else {
            $model = new ModeMapModel;
        }

        $model->wounded_child_mode = $modeMap->getWoundedChildMode();
        $model->hurtful_adult_mode = $modeMap->getHurtfulAdultMode();
        $model->unacceptable_coping_mode = $modeMap->getUnacceptableCopingMode();
        $model->healthy_happy_child_mode = $modeMap->getHealthyHappyChildMode();
        $model->healthy_adult_mode = $modeMap->getHealthyAdultMode();
        $model->save();

        return $this->toEntity($model);
    }

    public function findById(int $id): ?ModeMapEntity
    {
        $model = ModeMapModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findFirst(): ?ModeMapEntity
    {
        $model = ModeMapModel::first();

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    private function toEntity(ModeMapModel $model): ModeMapEntity
    {
        return ModeMapEntity::reconstitute(
            id: (int) $model->getKey(),
            woundedChildMode: $model->wounded_child_mode,
            hurtfulAdultMode: $model->hurtful_adult_mode,
            unacceptableCopingMode: $model->unacceptable_coping_mode,
            healthyHappyChildMode: $model->healthy_happy_child_mode,
            healthyAdultMode: $model->healthy_adult_mode,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }
}
