<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\CopingTag as CopingTagEntity;
use App\Domain\Repository\CopingTagRepositoryInterface;
use App\Infrastructure\Database\Models\CopingTag as CopingTagModel;
use DateTimeImmutable;

class EloquentCopingTagRepository implements CopingTagRepositoryInterface
{
    public function save(CopingTagEntity $copingTag): CopingTagEntity
    {
        if ($copingTag->getId() !== null) {
            // 更新
            $model = CopingTagModel::findOrFail($copingTag->getId());
            $model->name = $copingTag->getName();
            $model->save();
        } else {
            // 新規作成
            $model = new CopingTagModel();
            $model->name = $copingTag->getName();
            $model->save();
        }

        return CopingTagEntity::reconstitute(
            id: (int) $model->getKey(),
            name: (string) $model->name,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findById(int $id): ?CopingTagEntity
    {
        $model = CopingTagModel::find($id);

        if ($model === null) {
            return null;
        }

        return CopingTagEntity::reconstitute(
            id: (int) $model->id,
            name: (string) $model->name,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function delete(int $id): void
    {
        $model = CopingTagModel::find($id);

        if ($model !== null) {
            $model->copings()->detach();
            $model->delete();
        }
    }
}
