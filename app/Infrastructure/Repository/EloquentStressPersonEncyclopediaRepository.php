<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\StressPersonEncyclopedia as StressPersonEncyclopediaEntity;
use App\Domain\Repository\StressPersonEncyclopediaRepositoryInterface;
use App\Infrastructure\Database\Models\StressPersonEncyclopedia as StressPersonEncyclopediaModel;

class EloquentStressPersonEncyclopediaRepository implements StressPersonEncyclopediaRepositoryInterface
{
    private function toEntity(StressPersonEncyclopediaModel $model): StressPersonEncyclopediaEntity
    {
        return StressPersonEncyclopediaEntity::reconstitute(
            id: (int) $model->id,
            name: (string) $model->name,
            relationship: $model->relationship,
            difficultTraits: $model->difficult_traits,
            myReaction: $model->my_reaction,
            copingStrategy: $model->coping_strategy,
            notes: $model->notes,
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }

    public function findAllForMember(int $memberId): array
    {
        return StressPersonEncyclopediaModel::where('member_id', $memberId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($model) => $this->toEntity($model))
            ->values()
            ->all();
    }

    public function saveForMember(StressPersonEncyclopediaEntity $encyclopedia, int $memberId): StressPersonEncyclopediaEntity
    {
        if ($encyclopedia->getId() !== null) {
            $model = StressPersonEncyclopediaModel::where('member_id', $memberId)
                ->findOrFail($encyclopedia->getId());
        } else {
            $model = new StressPersonEncyclopediaModel();
            $model->member_id = $memberId;
        }

        $model->name = $encyclopedia->getName();
        $model->relationship = $encyclopedia->getRelationship();
        $model->difficult_traits = $encyclopedia->getDifficultTraits();
        $model->my_reaction = $encyclopedia->getMyReaction();
        $model->coping_strategy = $encyclopedia->getCopingStrategy();
        $model->notes = $encyclopedia->getNotes();
        $model->save();

        return $this->toEntity($model);
    }

    public function findByIdForMember(int $id, int $memberId): ?StressPersonEncyclopediaEntity
    {
        $model = StressPersonEncyclopediaModel::where('member_id', $memberId)->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function deleteForMember(int $id, int $memberId): void
    {
        $model = StressPersonEncyclopediaModel::where('member_id', $memberId)->find($id);

        if ($model !== null) {
            $model->delete();
        }
    }
}
