<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Coping as CopingEntity;
use App\Domain\Repository\CopingRepositoryInterface;
use App\Infrastructure\Database\Models\Coping as CopingModel;
use DateTimeImmutable;

class EloquentCopingRepository implements CopingRepositoryInterface
{
    public function save(CopingEntity $coping): CopingEntity
    {
        if ($coping->getId() !== null) {
            // 更新
            $model = CopingModel::findOrFail($coping->getId());
            $model->content = $coping->getContent();
            $model->point = $coping->getPoint();
            $model->save();
        } else {
            // 新規作成
            $model = new CopingModel();
            $model->content = $coping->getContent();
            $model->point = $coping->getPoint();
            $model->save();
        }

        // タグを中間テーブルに保存
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

    public function findById(int $id): ?CopingEntity
    {
        $model = CopingModel::with('copingTags')->find($id);

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

    public function delete(int $id): void
    {
        $model = CopingModel::find($id);

        if ($model !== null) {
            $model->copingTags()->detach();
            $model->delete();
        }
    }
}
