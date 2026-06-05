<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\SimpleNotepad as SimpleNotepadEntity;
use App\Domain\Repository\SimpleNotepadRepositoryInterface;
use App\Infrastructure\Database\Models\SimpleNotepad as SimpleNotepadModel;
use Carbon\Carbon;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

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

    public function findAllWithTagsForMember(int $memberId): array
    {
        return SimpleNotepadModel::where('member_id', $memberId)
            ->with('tags')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($model) => $this->formatWithTags(
                SimpleNotepadEntity::reconstitute(
                    id: (int) $model->id,
                    title: (string) $model->title,
                    content: (string) $model->content,
                    createdAt: $this->toDateTimeImmutable($model->created_at),
                    updatedAt: $this->toDateTimeImmutable($model->updated_at),
                ),
                $model
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

    public function saveWithTagsForMember(SimpleNotepadEntity $simpleNotepad, array $tagIds, int $memberId): array
    {
        return DB::transaction(function () use ($simpleNotepad, $tagIds, $memberId) {
            $savedSimpleNotepad = $this->saveForMember($simpleNotepad, $memberId);

            $model = SimpleNotepadModel::with('tags')
                ->where('member_id', $memberId)
                ->findOrFail($savedSimpleNotepad->getId());

            $validTagIds = DB::table('simple_notepad_tags')
                ->where('member_id', $memberId)
                ->whereIn('id', $tagIds)
                ->pluck('id')
                ->toArray();

            $model->tags()->sync($validTagIds);
            $model->load('tags');

            return $this->formatWithTags($savedSimpleNotepad, $model);
        });
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

    /**
     * @return array<string, mixed>
     */
    private function formatWithTags(SimpleNotepadEntity $simpleNotepad, SimpleNotepadModel $model): array
    {
        return [
            'id' => $simpleNotepad->getId(),
            'title' => $simpleNotepad->getTitle(),
            'content' => $simpleNotepad->getContent(),
            'tags' => $model->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])->values()->toArray(),
            'tag_ids' => $model->tags->pluck('id')->values()->toArray(),
            'created_at' => $simpleNotepad->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $simpleNotepad->getUpdatedAt()->format(DATE_ATOM),
        ];
    }
}
