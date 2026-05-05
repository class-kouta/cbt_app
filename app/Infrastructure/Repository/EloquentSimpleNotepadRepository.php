<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\SimpleNotepad as SimpleNotepadEntity;
use App\Domain\Repository\SimpleNotepadRepositoryInterface;
use App\Infrastructure\Database\Models\SimpleNotepad as SimpleNotepadModel;
use Carbon\Carbon;
use DateTimeImmutable;

class EloquentSimpleNotepadRepository implements SimpleNotepadRepositoryInterface
{
    private function toDateTimeImmutable(mixed $value): DateTimeImmutable
    {
        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof Carbon) {
            return DateTimeImmutable::createFromMutable($value);
        }

        return new DateTimeImmutable((string) $value);
    }

    public function findAllForMember(int $memberId): array
    {
        return SimpleNotepadModel::where('member_id', $memberId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($model) => SimpleNotepadEntity::reconstitute(
                id: (int) $model->id,
                title: (string) $model->title,
                content: (string) $model->content,
                createdAt: $this->toDateTimeImmutable($model->created_at),
                updatedAt: $this->toDateTimeImmutable($model->updated_at),
            ))
            ->all();
    }

    public function saveForMember(SimpleNotepadEntity $simpleNotepad, int $memberId): SimpleNotepadEntity
    {
        if ($simpleNotepad->getId() !== null) {
            $model = SimpleNotepadModel::where('member_id', $memberId)
                ->findOrFail($simpleNotepad->getId());
            $model->title = $simpleNotepad->getTitle();
            $model->content = $simpleNotepad->getContent();
            $model->save();
        } else {
            $model = new SimpleNotepadModel();
            $model->member_id = $memberId;
            $model->title = $simpleNotepad->getTitle();
            $model->content = $simpleNotepad->getContent();
            $model->save();
        }

        return SimpleNotepadEntity::reconstitute(
            id: (int) $model->getKey(),
            title: (string) $model->title,
            content: (string) $model->content,
            createdAt: $this->toDateTimeImmutable($model->created_at),
            updatedAt: $this->toDateTimeImmutable($model->updated_at),
        );
    }

    public function findByIdForMember(int $id, int $memberId): ?SimpleNotepadEntity
    {
        $model = SimpleNotepadModel::where('member_id', $memberId)->find($id);

        if ($model === null) {
            return null;
        }

        return SimpleNotepadEntity::reconstitute(
            id: (int) $model->id,
            title: (string) $model->title,
            content: (string) $model->content,
            createdAt: $this->toDateTimeImmutable($model->created_at),
            updatedAt: $this->toDateTimeImmutable($model->updated_at),
        );
    }

    public function deleteForMember(int $id, int $memberId): void
    {
        $model = SimpleNotepadModel::where('member_id', $memberId)->find($id);

        if ($model !== null) {
            $model->delete();
        }
    }
}
