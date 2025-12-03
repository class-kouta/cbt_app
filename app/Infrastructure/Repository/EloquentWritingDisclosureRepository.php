<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\WritingDisclosure as WritingDisclosureEntity;
use App\Domain\Repository\WritingDisclosureRepositoryInterface;
use App\Infrastructure\Database\Models\WritingDisclosure as WritingDisclosureModel;
use DateTimeImmutable;

class EloquentWritingDisclosureRepository implements WritingDisclosureRepositoryInterface
{
    public function save(WritingDisclosureEntity $writingDisclosure): WritingDisclosureEntity
    {
        if ($writingDisclosure->getId() !== null) {
            // 更新
            $model = WritingDisclosureModel::findOrFail($writingDisclosure->getId());
            $model->content = $writingDisclosure->getContent();
            $model->save();
        } else {
            // 新規作成
            $model = new WritingDisclosureModel();
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

    public function findById(int $id): ?WritingDisclosureEntity
    {
        $model = WritingDisclosureModel::find($id);

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

    public function delete(int $id): void
    {
        $model = WritingDisclosureModel::find($id);

        if ($model !== null) {
            $model->delete();
        }
    }
}
