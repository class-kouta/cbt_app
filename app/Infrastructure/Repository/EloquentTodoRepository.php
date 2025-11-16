<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Todo as TodoEntity;
use App\Domain\Repository\TodoRepositoryInterface;
use App\Infrastructure\Database\Models\Todo as TodoModel;
use DateTimeImmutable;

class EloquentTodoRepository implements TodoRepositoryInterface
{
    public function save(TodoEntity $todo): TodoEntity
    {
        $model = new TodoModel();
        $model->difficulty_id = $todo->getDifficultyId();
        $model->content = $todo->getContent();
        $model->completed_at = $todo->getCompletedAt()?->format('Y-m-d H:i:s');
        $model->save();

        return TodoEntity::reconstitute(
            id: (int) $model->getKey(),
            difficultyId: (int) $model->difficulty_id,
            content: (string) $model->content,
            completedAt: $model->completed_at ? new DateTimeImmutable($model->completed_at) : null,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
            tagIds: $todo->getTagIds(),
        );
    }
}

