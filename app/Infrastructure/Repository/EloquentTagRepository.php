<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Tag as TagEntity;
use App\Domain\Repository\TagRepositoryInterface;
use App\Infrastructure\Database\Models\Tag as TagModel;
use DateTimeImmutable;

class EloquentTagRepository implements TagRepositoryInterface
{
    public function save(TagEntity $tag): TagEntity
    {
        $model = TagModel::updateOrCreate(
            ['id' => $tag->getId()],
            ['name' => $tag->getName()]
        );

        return TagEntity::reconstitute(
            id: (int) $model->getKey(),
            name: (string) $model->name,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findById(int $id): ?TagEntity
    {
        $model = TagModel::find($id);

        if ($model === null) {
            return null;
        }

        return TagEntity::reconstitute(
            id: (int) $model->id,
            name: (string) $model->name,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    /**
     * @return TagEntity[]
     */
    public function findAll(): array
    {
        $models = TagModel::orderBy('id')->get();

        return $models->map(function (TagModel $model) {
            return TagEntity::reconstitute(
                id: (int) $model->id,
                name: (string) $model->name,
                createdAt: new DateTimeImmutable($model->created_at),
                updatedAt: new DateTimeImmutable($model->updated_at),
            );
        })->all();
    }

    public function delete(int $id): void
    {
        TagModel::destroy($id);
    }
}
