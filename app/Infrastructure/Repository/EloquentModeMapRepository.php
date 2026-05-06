<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\ModeMap as ModeMapEntity;
use App\Domain\Repository\ModeMapRepositoryInterface;
use App\Infrastructure\Database\Models\ModeMap as ModeMapModel;
use DateTimeImmutable;

class EloquentModeMapRepository implements ModeMapRepositoryInterface
{
    public function saveForMember(ModeMapEntity $modeMap, int $memberId): ModeMapEntity
    {
        $model = ModeMapModel::updateOrCreate(
            ['member_id' => $memberId],
            [
                'wounded_child_mode' => $modeMap->getWoundedChildMode(),
                'hurtful_adult_mode' => $modeMap->getHurtfulAdultMode(),
                'unacceptable_coping_mode' => $modeMap->getUnacceptableCopingMode(),
                'healthy_happy_child_mode' => $modeMap->getHealthyHappyChildMode(),
                'healthy_adult_mode' => $modeMap->getHealthyAdultMode(),
            ],
        );

        return $this->toEntity($model);
    }

    public function findByIdForMember(int $id, int $memberId): ?ModeMapEntity
    {
        $model = ModeMapModel::where('member_id', $memberId)->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findFirstForMember(int $memberId): ?ModeMapEntity
    {
        $model = ModeMapModel::where('member_id', $memberId)->first();

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
