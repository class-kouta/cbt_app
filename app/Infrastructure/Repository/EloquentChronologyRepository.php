<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Chronology as ChronologyEntity;
use App\Domain\Repository\ChronologyRepositoryInterface;
use App\Infrastructure\Database\Models\Chronology as ChronologyModel;
use DateTimeImmutable;

class EloquentChronologyRepository implements ChronologyRepositoryInterface
{
    public function findAllForMember(int $memberId): array
    {
        return ChronologyModel::where('member_id', $memberId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (ChronologyModel $model) => $this->toEntity($model))
            ->all();
    }

    public function saveForMember(ChronologyEntity $chronology, int $memberId): ChronologyEntity
    {
        $model = $chronology->getId() === null
            ? new ChronologyModel()
            : ChronologyModel::where('id', $chronology->getId())
                ->where('member_id', $memberId)
                ->firstOrFail();

        $model->member_id = $memberId;
        $model->when_period = $chronology->getWhenPeriod();
        $model->environment_event = $chronology->getEnvironmentEvent();
        $model->experience_feeling = $chronology->getExperienceFeeling();
        $model->sentiment_type = $chronology->getSentimentType();
        $model->save();

        return $this->toEntity($model);
    }

    public function findByIdForMember(int $id, int $memberId): ?ChronologyEntity
    {
        $model = ChronologyModel::where('id', $id)
            ->where('member_id', $memberId)
            ->first();

        return $model ? $this->toEntity($model) : null;
    }

    public function deleteForMember(int $id, int $memberId): void
    {
        ChronologyModel::where('id', $id)
            ->where('member_id', $memberId)
            ->delete();
    }

    private function toEntity(ChronologyModel $model): ChronologyEntity
    {
        return ChronologyEntity::reconstitute(
            id: (int) $model->id,
            whenPeriod: (string) $model->when_period,
            environmentEvent: $model->environment_event,
            experienceFeeling: $model->experience_feeling,
            sentimentType: $model->sentiment_type,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }
}
