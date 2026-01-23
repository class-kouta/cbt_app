<?php

namespace App\Infrastructure\Repository;

use App\Application\DTO\SearchCriteriaData;
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
            $model->stimulated_schemas = $stressorAndResponse->getStimulatedSchemas();
            $model->save();
        } else {
            // 新規作成
            $model = new StressorAndResponseModel();
            $model->stressor = $stressorAndResponse->getStressor();
            $model->cognition = $stressorAndResponse->getCognition();
            $model->mood = $stressorAndResponse->getMood();
            $model->body_reaction = $stressorAndResponse->getBodyReaction();
            $model->behavior = $stressorAndResponse->getBehavior();
            $model->stimulated_schemas = $stressorAndResponse->getStimulatedSchemas();
            $model->save();
        }

        return StressorAndResponseEntity::reconstitute(
            id: (int) $model->getKey(),
            stressor: (string) $model->stressor,
            cognition: $model->cognition,
            mood: $model->mood,
            bodyReaction: $model->body_reaction,
            behavior: $model->behavior,
            stimulatedSchemas: $model->stimulated_schemas,
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
            stimulatedSchemas: $model->stimulated_schemas,
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
                    stimulatedSchemas: $model->stimulated_schemas,
                    createdAt: new DateTimeImmutable($model->created_at),
                    updatedAt: new DateTimeImmutable($model->updated_at),
                );
            })
            ->toArray();
    }

    /**
     * 検索条件に基づいてストレッサーとストレス反応を検索
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<int, array<string, mixed>> 検索結果（配列形式）
     */
    public function search(SearchCriteriaData $criteria, array $searchableColumns): array
    {
        $query = StressorAndResponseModel::with('tags');

        // キーワード検索
        if ($criteria->hasKeyword() && count($searchableColumns) > 0) {
            $keyword = $criteria->keyword;
            $query->where(function ($q) use ($keyword, $searchableColumns) {
                foreach ($searchableColumns as $index => $column) {
                    if ($index === 0) {
                        $q->where($column, 'like', "%{$keyword}%");
                    } else {
                        $q->orWhere($column, 'like', "%{$keyword}%");
                    }
                }
            });
        }

        // タグ検索
        if ($criteria->hasTagIds()) {
            $query->whereHas('tags', function ($q) use ($criteria) {
                $q->whereIn('tags.id', $criteria->tagIds);
            });
        }

        return $query->orderByDesc('created_at')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'stressor' => $item->stressor,
                    'cognition' => $item->cognition,
                    'mood' => $item->mood,
                    'body_reaction' => $item->body_reaction,
                    'behavior' => $item->behavior,
                    'stimulated_schemas' => $item->stimulated_schemas,
                    'tags' => $item->tags->map(fn ($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ])->toArray(),
                    'created_at' => $item->created_at->format(DATE_ATOM),
                    'updated_at' => $item->updated_at->format(DATE_ATOM),
                ];
            })
            ->toArray();
    }
}
