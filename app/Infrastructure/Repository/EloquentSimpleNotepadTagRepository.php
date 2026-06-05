<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\SimpleNotepadTag as SimpleNotepadTagEntity;
use App\Domain\Repository\SimpleNotepadTagRepositoryInterface;
use App\Infrastructure\Database\Models\SimpleNotepadTag as SimpleNotepadTagModel;
use Carbon\Carbon;
use DateTimeImmutable;

class EloquentSimpleNotepadTagRepository implements SimpleNotepadTagRepositoryInterface
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
        return SimpleNotepadTagModel::where('member_id', $memberId)
            ->orderBy('name')
            ->get()
            ->map(fn ($model) => SimpleNotepadTagEntity::reconstitute(
                id: (int) $model->id,
                name: (string) $model->name,
                createdAt: $this->toDateTimeImmutable($model->created_at),
                updatedAt: $this->toDateTimeImmutable($model->updated_at),
            ))
            ->all();
    }

    public function findAllSummariesForMember(int $memberId): array
    {
        return SimpleNotepadTagModel::where('member_id', $memberId)
            ->withCount('simpleNotepads')
            ->orderBy('name')
            ->get()
            ->map(fn ($model) => [
                'id' => (int) $model->id,
                'name' => (string) $model->name,
                'usage_count' => (int) $model->simple_notepads_count,
                'created_at' => $this->toDateTimeImmutable($model->created_at)->format(DATE_ATOM),
                'updated_at' => $this->toDateTimeImmutable($model->updated_at)->format(DATE_ATOM),
            ])
            ->all();
    }

    public function countForMember(int $memberId): int
    {
        return SimpleNotepadTagModel::where('member_id', $memberId)->count();
    }

    public function saveForMember(SimpleNotepadTagEntity $tag, int $memberId): SimpleNotepadTagEntity
    {
        if ($tag->getId() !== null) {
            $model = SimpleNotepadTagModel::where('member_id', $memberId)
                ->findOrFail($tag->getId());
            $model->name = $tag->getName();
            $model->save();
        } else {
            $model = new SimpleNotepadTagModel();
            $model->member_id = $memberId;
            $model->name = $tag->getName();
            $model->save();
        }

        return SimpleNotepadTagEntity::reconstitute(
            id: (int) $model->getKey(),
            name: (string) $model->name,
            createdAt: $this->toDateTimeImmutable($model->created_at),
            updatedAt: $this->toDateTimeImmutable($model->updated_at),
        );
    }

    public function findByIdForMember(int $id, int $memberId): ?SimpleNotepadTagEntity
    {
        $model = SimpleNotepadTagModel::where('member_id', $memberId)->find($id);

        if ($model === null) {
            return null;
        }

        return SimpleNotepadTagEntity::reconstitute(
            id: (int) $model->id,
            name: (string) $model->name,
            createdAt: $this->toDateTimeImmutable($model->created_at),
            updatedAt: $this->toDateTimeImmutable($model->updated_at),
        );
    }

    public function deleteForMember(int $id, int $memberId): void
    {
        $model = SimpleNotepadTagModel::where('member_id', $memberId)->find($id);

        if ($model !== null) {
            $model->delete();
        }
    }
}
