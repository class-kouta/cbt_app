<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Coping as CopingEntity;
use App\Domain\Repository\CopingRepositoryInterface;
use App\Infrastructure\Database\Models\Coping as CopingModel;
use DateTimeImmutable;

class EloquentCopingRepository implements CopingRepositoryInterface
{
    public function findAllForMember(int $memberId): array
    {
        return CopingModel::with('copingTags')
            ->where('member_id', $memberId)
            ->orderByDesc('point')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (CopingModel $model) => CopingEntity::reconstitute(
                id: (int) $model->id,
                content: (string) $model->content,
                point: (int) $model->point,
                createdAt: new DateTimeImmutable($model->created_at),
                updatedAt: new DateTimeImmutable($model->updated_at),
                copingTagIds: $model->copingTags->pluck('id')->map(fn ($id) => (int) $id)->toArray(),
            ))
            ->all();
    }

    public function saveForMember(CopingEntity $coping, int $memberId): CopingEntity
    {
        if ($coping->getId() !== null) {
            $model = CopingModel::where('member_id', $memberId)->findOrFail($coping->getId());
            $model->content = $coping->getContent();
            $model->point = $coping->getPoint();
            $model->save();
        } else {
            $model = new CopingModel();
            $model->member_id = $memberId;
            $model->content = $coping->getContent();
            $model->point = $coping->getPoint();
            $model->save();
        }

        $model->copingTags()->sync($coping->getCopingTagIds());

        return CopingEntity::reconstitute(
            id: (int) $model->getKey(),
            content: (string) $model->content,
            point: (int) $model->point,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
            copingTagIds: $coping->getCopingTagIds(),
        );
    }

    public function findByIdForMember(int $id, int $memberId): ?CopingEntity
    {
        $model = CopingModel::with('copingTags')
            ->where('member_id', $memberId)
            ->find($id);

        if ($model === null) {
            return null;
        }

        return CopingEntity::reconstitute(
            id: (int) $model->id,
            content: (string) $model->content,
            point: (int) $model->point,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
            copingTagIds: $model->copingTags->pluck('id')->map(fn ($id) => (int) $id)->toArray(),
        );
    }

    public function deleteForMember(int $id, int $memberId): void
    {
        $model = CopingModel::where('member_id', $memberId)->find($id);

        if ($model !== null) {
            $model->copingTags()->detach();
            $model->delete();
        }
    }
}
