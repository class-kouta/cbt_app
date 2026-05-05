<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\DialogueWork as DialogueWorkEntity;
use App\Domain\Repository\DialogueWorkRepositoryInterface;
use App\Infrastructure\Database\Models\DialogueWork as DialogueWorkModel;
use DateTimeImmutable;

class EloquentDialogueWorkRepository implements DialogueWorkRepositoryInterface
{
    public function saveForMember(DialogueWorkEntity $dialogueWork, int $memberId): DialogueWorkEntity
    {
        if ($dialogueWork->getId() !== null) {
            $model = DialogueWorkModel::where('member_id', $memberId)->findOrFail($dialogueWork->getId());
        } else {
            $model = new DialogueWorkModel();
            $model->member_id = $memberId;
            $model->type = 'healthy';
        }

        $model->content = $dialogueWork->getContent();
        $model->save();

        return DialogueWorkEntity::reconstitute(
            id: (int) $model->getKey(),
            content: (string) $model->content,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findByIdForMember(int $id, int $memberId): ?DialogueWorkEntity
    {
        $model = DialogueWorkModel::where('member_id', $memberId)
            ->where('type', 'healthy')
            ->find($id);

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

    public function findAllForMemberOrderByCreatedAtDesc(int $memberId): array
    {
        return DialogueWorkModel::where('member_id', $memberId)
            ->where('type', 'healthy')
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

    public function deleteForMember(int $id, int $memberId): void
    {
        $model = DialogueWorkModel::where('member_id', $memberId)
            ->where('type', 'healthy')
            ->find($id);

        if ($model !== null) {
            $model->delete();
        }
    }
}
