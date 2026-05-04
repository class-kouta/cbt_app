<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\WritingDisclosure as WritingDisclosureEntity;
use App\Domain\Repository\WritingDisclosureRepositoryInterface;
use App\Infrastructure\Database\Models\WritingDisclosure as WritingDisclosureModel;
use DateTimeImmutable;

class EloquentWritingDisclosureRepository implements WritingDisclosureRepositoryInterface
{
    public function findAllForMember(int $memberId): array
    {
        return WritingDisclosureModel::where('member_id', $memberId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($model) => WritingDisclosureEntity::reconstitute(
                id: (int) $model->id,
                content: (string) $model->content,
                createdAt: new DateTimeImmutable($model->created_at),
                updatedAt: new DateTimeImmutable($model->updated_at),
            ))
            ->all();
    }

    public function saveForMember(WritingDisclosureEntity $writingDisclosure, int $memberId): WritingDisclosureEntity
    {
        if ($writingDisclosure->getId() !== null) {
            $model = WritingDisclosureModel::where('member_id', $memberId)
                ->findOrFail($writingDisclosure->getId());
            $model->content = $writingDisclosure->getContent();
            $model->save();
        } else {
            $model = new WritingDisclosureModel();
            $model->member_id = $memberId;
            $model->content = $writingDisclosure->getContent();
            $model->save();
        }

        return WritingDisclosureEntity::reconstitute(
            id: (int) $model->getKey(),
            content: (string) $model->content,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findByIdForMember(int $id, int $memberId): ?WritingDisclosureEntity
    {
        $model = WritingDisclosureModel::where('member_id', $memberId)->find($id);

        if ($model === null) {
            return null;
        }

        return WritingDisclosureEntity::reconstitute(
            id: (int) $model->id,
            content: (string) $model->content,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function deleteForMember(int $id, int $memberId): void
    {
        $model = WritingDisclosureModel::where('member_id', $memberId)->find($id);

        if ($model !== null) {
            $model->delete();
        }
    }
}
