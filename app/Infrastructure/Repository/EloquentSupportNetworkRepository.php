<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\SupportNetwork as SupportNetworkEntity;
use App\Domain\Repository\SupportNetworkRepositoryInterface;
use App\Infrastructure\Database\Models\SupportNetwork as SupportNetworkModel;
use DateTimeImmutable;

class EloquentSupportNetworkRepository implements SupportNetworkRepositoryInterface
{
    public function findAllForMember(int $memberId): array
    {
        return SupportNetworkModel::where('member_id', $memberId)
            ->orderByDesc('point')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($model) => SupportNetworkEntity::reconstitute(
                id: (int) $model->id,
                name: (string) $model->name,
                point: (int) $model->point,
                createdAt: new DateTimeImmutable($model->created_at),
                updatedAt: new DateTimeImmutable($model->updated_at),
            ))
            ->all();
    }

    public function saveForMember(SupportNetworkEntity $supportNetwork, int $memberId): SupportNetworkEntity
    {
        if ($supportNetwork->getId() !== null) {
            $model = SupportNetworkModel::where('member_id', $memberId)
                ->findOrFail($supportNetwork->getId());
            $model->name = $supportNetwork->getName();
            $model->point = $supportNetwork->getPoint();
            $model->save();
        } else {
            $model = new SupportNetworkModel();
            $model->member_id = $memberId;
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

    public function findByIdForMember(int $id, int $memberId): ?SupportNetworkEntity
    {
        $model = SupportNetworkModel::where('member_id', $memberId)->find($id);

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

    public function deleteForMember(int $id, int $memberId): void
    {
        $model = SupportNetworkModel::where('member_id', $memberId)->find($id);

        if ($model !== null) {
            $model->delete();
        }
    }
}
