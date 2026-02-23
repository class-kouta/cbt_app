<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Chronology as ChronologyEntity;
use App\Domain\Repository\ChronologyRepositoryInterface;
use App\Infrastructure\Database\Models\Chronology as ChronologyModel;
use DateTimeImmutable;

class EloquentChronologyRepository implements ChronologyRepositoryInterface
{
    public function save(ChronologyEntity $chronology): ChronologyEntity
    {
        $model = ChronologyModel::updateOrCreate(
            ['id' => $chronology->getId()],
            [
                'when_period' => $chronology->getWhenPeriod(),
                'environment_event' => $chronology->getEnvironmentEvent(),
                'experience_feeling' => $chronology->getExperienceFeeling(),
            ]
        );

        return ChronologyEntity::reconstitute(
            id: (int) $model->getKey(),
            whenPeriod: (string) $model->when_period,
            environmentEvent: $model->environment_event,
            experienceFeeling: $model->experience_feeling,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findById(int $id): ?ChronologyEntity
    {
        $model = ChronologyModel::find($id);

        if ($model === null) {
            return null;
        }

        return ChronologyEntity::reconstitute(
            id: (int) $model->id,
            whenPeriod: (string) $model->when_period,
            environmentEvent: $model->environment_event,
            experienceFeeling: $model->experience_feeling,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function delete(int $id): void
    {
        ChronologyModel::destroy($id);
    }
}
