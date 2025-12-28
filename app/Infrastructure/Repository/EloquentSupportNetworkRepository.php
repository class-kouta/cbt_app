<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\SupportNetwork as SupportNetworkEntity;
use App\Domain\Repository\SupportNetworkRepositoryInterface;
use App\Infrastructure\Database\Models\SupportNetwork as SupportNetworkModel;
use DateTimeImmutable;

class EloquentSupportNetworkRepository implements SupportNetworkRepositoryInterface
{
    public function save(SupportNetworkEntity $supportNetwork): SupportNetworkEntity
    {
        if ($supportNetwork->getId() !== null) {
            // 更新
            $model = SupportNetworkModel::findOrFail($supportNetwork->getId());
            $model->name = $supportNetwork->getName();
            $model->point = $supportNetwork->getPoint();
            $model->save();
        } else {
            // 新規作成
            $model = new SupportNetworkModel();
            $model->name = $supportNetwork->getName();
            $model->point = $supportNetwork->getPoint();
            $model->save();
        }

        return SupportNetworkEntity::reconstitute(
            id: (int) $model->getKey(),
            name: (string) $model->name,
            point: (int) $model->point,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findById(int $id): ?SupportNetworkEntity
    {
        $model = SupportNetworkModel::find($id);

        if ($model === null) {
            return null;
        }

        return SupportNetworkEntity::reconstitute(
            id: (int) $model->id,
            name: (string) $model->name,
            point: (int) $model->point,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function delete(int $id): void
    {
        $model = SupportNetworkModel::find($id);

        if ($model !== null) {
            $model->delete();
        }
    }
}
