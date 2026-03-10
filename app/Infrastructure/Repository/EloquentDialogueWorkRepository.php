<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\DialogueWork as DialogueWorkEntity;
use App\Domain\Repository\DialogueWorkRepositoryInterface;
use App\Infrastructure\Database\Models\DialogueWork as DialogueWorkModel;
use DateTimeImmutable;

class EloquentDialogueWorkRepository implements DialogueWorkRepositoryInterface
{
    public function save(DialogueWorkEntity $dialogueWork): DialogueWorkEntity
    {
        $model = DialogueWorkModel::updateOrCreate(
            ['id' => $dialogueWork->getId()],
            ['content' => $dialogueWork->getContent()]
        );

        return DialogueWorkEntity::reconstitute(
            id: (int) $model->getKey(),
            content: (string) $model->content,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findById(int $id): ?DialogueWorkEntity
    {
        $model = DialogueWorkModel::find($id);

        if ($model === null) {
            return null;
        }

        return DialogueWorkEntity::reconstitute(
            id: (int) $model->id,
            content: (string) $model->content,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findAllOrderByCreatedAtDesc(): array
    {
        return DialogueWorkModel::where('type', 'schema')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($model) {
                return [
                    'id' => $model->id,
                    'content' => $model->content,
                    'created_at' => $model->created_at->format(DATE_ATOM),
                    'updated_at' => $model->updated_at->format(DATE_ATOM),
                ];
            })
            ->toArray();
    }

    public function delete(int $id): void
    {
        DialogueWorkModel::destroy($id);
    }
}
