<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\SimpleNotepad as SimpleNotepadEntity;
use App\Domain\Repository\SimpleNotepadRepositoryInterface;
use App\Infrastructure\Database\Models\SimpleNotepad as SimpleNotepadModel;
use DateTimeImmutable;

class EloquentSimpleNotepadRepository implements SimpleNotepadRepositoryInterface
{
    public function save(SimpleNotepadEntity $simpleNotepad): SimpleNotepadEntity
    {
        if ($simpleNotepad->getId() !== null) {
            // 更新
            $model = SimpleNotepadModel::findOrFail($simpleNotepad->getId());
            $model->content = $simpleNotepad->getContent();
            $model->save();
        } else {
            // 新規作成
            $model = new SimpleNotepadModel();
            $model->content = $simpleNotepad->getContent();
            $model->save();
        }

        return SimpleNotepadEntity::reconstitute(
            id: (int) $model->getKey(),
            content: (string) $model->content,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findById(int $id): ?SimpleNotepadEntity
    {
        $model = SimpleNotepadModel::find($id);

        if ($model === null) {
            return null;
        }

        return SimpleNotepadEntity::reconstitute(
            id: (int) $model->id,
            content: (string) $model->content,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function delete(int $id): void
    {
        $model = SimpleNotepadModel::find($id);

        if ($model !== null) {
            $model->delete();
        }
    }
}
