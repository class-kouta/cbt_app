<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\HealthyAdultModeImage as HealthyAdultModeImageEntity;
use App\Domain\Repository\HealthyAdultModeImageRepositoryInterface;
use App\Infrastructure\Database\Models\HealthyAdultModeImage as HealthyAdultModeImageModel;
use DateTimeImmutable;

class EloquentHealthyAdultModeImageRepository implements HealthyAdultModeImageRepositoryInterface
{
    public function saveForMember(HealthyAdultModeImageEntity $entity, int $memberId): HealthyAdultModeImageEntity
    {
        if ($entity->getId() !== null) {
            $model = HealthyAdultModeImageModel::where('member_id', $memberId)
                ->findOrFail($entity->getId());
        } else {
            $model = HealthyAdultModeImageModel::firstOrNew(['member_id' => $memberId]);
        }

        $model->content = $entity->getContent();
        $model->save();

        return $this->toEntity($model);
    }

    public function findByIdForMember(int $id, int $memberId): ?HealthyAdultModeImageEntity
    {
        $model = HealthyAdultModeImageModel::where('member_id', $memberId)->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findFirstForMember(int $memberId): ?HealthyAdultModeImageEntity
    {
        $model = HealthyAdultModeImageModel::where('member_id', $memberId)->first();

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
