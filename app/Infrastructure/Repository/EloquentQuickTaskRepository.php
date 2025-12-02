<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\QuickTask as QuickTaskEntity;
use App\Domain\Repository\QuickTaskRepositoryInterface;
use App\Infrastructure\Database\Models\QuickTask as QuickTaskModel;
use DateTimeImmutable;

class EloquentQuickTaskRepository implements QuickTaskRepositoryInterface
{
    public function save(QuickTaskEntity $quickTask): QuickTaskEntity
    {
        if ($quickTask->getId() !== null) {
            // 更新
            $model = QuickTaskModel::findOrFail($quickTask->getId());
            $model->content = $quickTask->getContent();
            $model->difficulty_id = $quickTask->getDifficultyId();
            $model->save();
        } else {
            // 新規作成
            $model = new QuickTaskModel();
            $model->content = $quickTask->getContent();
            $model->difficulty_id = $quickTask->getDifficultyId();
            $model->save();
        }

        // タグを中間テーブルに保存
        $model->tags()->sync($quickTask->getTagIds());

        return QuickTaskEntity::reconstitute(
            id: (int) $model->getKey(),
            content: (string) $model->content,
            difficultyId: $model->difficulty_id ? (int) $model->difficulty_id : null,
            tagIds: $quickTask->getTagIds(),
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findById(int $id): ?QuickTaskEntity
    {
        $model = QuickTaskModel::with('tags')->find($id);

        if ($model === null) {
            return null;
        }

        return QuickTaskEntity::reconstitute(
            id: (int) $model->id,
            content: (string) $model->content,
            difficultyId: $model->difficulty_id ? (int) $model->difficulty_id : null,
            tagIds: $model->tags->pluck('id')->map(fn ($id) => (int) $id)->toArray(),
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    /**
     * @return QuickTaskEntity[]
     */
    public function findAll(): array
    {
        return QuickTaskModel::with('tags')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (QuickTaskModel $model) {
                return QuickTaskEntity::reconstitute(
                    id: (int) $model->id,
                    content: (string) $model->content,
                    difficultyId: $model->difficulty_id ? (int) $model->difficulty_id : null,
                    tagIds: $model->tags->pluck('id')->map(fn ($id) => (int) $id)->toArray(),
                    createdAt: new DateTimeImmutable($model->created_at),
                    updatedAt: new DateTimeImmutable($model->updated_at),
                );
            })
            ->toArray();
    }

    public function delete(int $id): void
    {
        $model = QuickTaskModel::find($id);

        if ($model !== null) {
            $model->tags()->detach();
            $model->delete();
        }
    }
}
