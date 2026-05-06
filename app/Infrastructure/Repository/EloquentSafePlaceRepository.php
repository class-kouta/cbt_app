<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\SafePlace as SafePlaceEntity;
use App\Domain\Repository\SafePlaceRepositoryInterface;
use App\Infrastructure\Database\Models\SafePlace as SafePlaceModel;
use DateTimeImmutable;

class EloquentSafePlaceRepository implements SafePlaceRepositoryInterface
{
    public function saveForMember(SafePlaceEntity $safePlace, int $memberId): SafePlaceEntity
    {
        if ($safePlace->getId() !== null) {
            $model = SafePlaceModel::where('member_id', $memberId)
                ->findOrFail($safePlace->getId());
        } else {
            $model = new SafePlaceModel();
            $model->member_id = $memberId;
        }

        $model->safe_image = $safePlace->getSafeImage();
        $model->safe_something = $safePlace->getSafeSomething();
        $model->save();

        return $this->toEntity($model);
    }

    public function findByIdForMember(int $id, int $memberId): ?SafePlaceEntity
    {
        $model = SafePlaceModel::where('member_id', $memberId)->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findFirstForMember(int $memberId): ?SafePlaceEntity
    {
        $model = SafePlaceModel::where('member_id', $memberId)->first();

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
