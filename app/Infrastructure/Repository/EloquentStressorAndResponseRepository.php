<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\StressorAndResponse as StressorAndResponseEntity;
use App\Domain\Repository\StressorAndResponseRepositoryInterface;
use App\Infrastructure\Database\Models\StressorAndResponse as StressorAndResponseModel;
use DateTimeImmutable;

class EloquentStressorAndResponseRepository implements StressorAndResponseRepositoryInterface
{
    public function save(StressorAndResponseEntity $stressorAndResponse): StressorAndResponseEntity
    {
        if ($stressorAndResponse->getId() !== null) {
            // 更新
            $model = StressorAndResponseModel::findOrFail($stressorAndResponse->getId());
            $model->stressor = $stressorAndResponse->getStressor();
            $model->cognition = $stressorAndResponse->getCognition();
            $model->mood = $stressorAndResponse->getMood();
            $model->body_reaction = $stressorAndResponse->getBodyReaction();
            $model->behavior = $stressorAndResponse->getBehavior();
            $model->save();
        } else {
            // 新規作成
            $model = new StressorAndResponseModel();
            $model->stressor = $stressorAndResponse->getStressor();
            $model->cognition = $stressorAndResponse->getCognition();
            $model->mood = $stressorAndResponse->getMood();
            $model->body_reaction = $stressorAndResponse->getBodyReaction();
            $model->behavior = $stressorAndResponse->getBehavior();
            $model->save();
        }

        return StressorAndResponseEntity::reconstitute(
            id: (int) $model->getKey(),
            stressor: (string) $model->stressor,
            cognition: $model->cognition,
            mood: $model->mood,
            bodyReaction: $model->body_reaction,
            behavior: $model->behavior,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findById(int $id): ?StressorAndResponseEntity
    {
        $model = StressorAndResponseModel::find($id);

        if ($model === null) {
            return null;
        }

        return StressorAndResponseEntity::reconstitute(
            id: (int) $model->id,
            stressor: (string) $model->stressor,
            cognition: $model->cognition,
            mood: $model->mood,
            bodyReaction: $model->body_reaction,
            behavior: $model->behavior,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function delete(int $id): void
    {
        $model = StressorAndResponseModel::find($id);

        if ($model !== null) {
            $model->delete();
        }
    }

    /**
     * @return StressorAndResponseEntity[]
     */
    public function findAll(): array
    {
        return StressorAndResponseModel::orderByDesc('created_at')
            ->get()
            ->map(function ($model) {
                return StressorAndResponseEntity::reconstitute(
                    id: (int) $model->id,
                    stressor: (string) $model->stressor,
                    cognition: $model->cognition,
                    mood: $model->mood,
                    bodyReaction: $model->body_reaction,
                    behavior: $model->behavior,
                    createdAt: new DateTimeImmutable($model->created_at),
                    updatedAt: new DateTimeImmutable($model->updated_at),
                );
            })
            ->toArray();
    }
}
