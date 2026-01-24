<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\AnxietyDiary as AnxietyDiaryEntity;
use App\Domain\Repository\AnxietyDiaryRepositoryInterface;
use App\Infrastructure\Database\Models\AnxietyDiary as AnxietyDiaryModel;
use DateTimeImmutable;

class EloquentAnxietyDiaryRepository implements AnxietyDiaryRepositoryInterface
{
    public function save(AnxietyDiaryEntity $anxietyDiary): AnxietyDiaryEntity
    {
        if ($anxietyDiary->getId() !== null) {
            // 更新
            $model = AnxietyDiaryModel::findOrFail($anxietyDiary->getId());
            $model->situation = $anxietyDiary->getSituation();
            $model->anxiety_thought = $anxietyDiary->getAnxietyThought();
            $model->actual_outcome = $anxietyDiary->getActualOutcome();
            $model->stressor_and_response_id = $anxietyDiary->getStressorAndResponseId();
            $model->save();
        } else {
            // 新規作成
            $model = new AnxietyDiaryModel();
            $model->situation = $anxietyDiary->getSituation();
            $model->anxiety_thought = $anxietyDiary->getAnxietyThought();
            $model->actual_outcome = $anxietyDiary->getActualOutcome();
            $model->stressor_and_response_id = $anxietyDiary->getStressorAndResponseId();
            $model->save();
        }

        return AnxietyDiaryEntity::reconstitute(
            id: (int) $model->getKey(),
            situation: (string) $model->situation,
            anxietyThought: $model->anxiety_thought,
            actualOutcome: $model->actual_outcome,
            stressorAndResponseId: $model->stressor_and_response_id,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function findById(int $id): ?AnxietyDiaryEntity
    {
        $model = AnxietyDiaryModel::find($id);

        if ($model === null) {
            return null;
        }

        return AnxietyDiaryEntity::reconstitute(
            id: (int) $model->id,
            situation: (string) $model->situation,
            anxietyThought: $model->anxiety_thought,
            actualOutcome: $model->actual_outcome,
            stressorAndResponseId: $model->stressor_and_response_id,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function delete(int $id): void
    {
        $model = AnxietyDiaryModel::find($id);

        if ($model !== null) {
            $model->delete();
        }
    }

    /**
     * @return AnxietyDiaryEntity[]
     */
    public function findAll(): array
    {
        return AnxietyDiaryModel::orderByDesc('created_at')
            ->get()
            ->map(function ($model) {
                return AnxietyDiaryEntity::reconstitute(
                    id: (int) $model->id,
                    situation: (string) $model->situation,
                    anxietyThought: $model->anxiety_thought,
                    actualOutcome: $model->actual_outcome,
                    stressorAndResponseId: $model->stressor_and_response_id,
                    createdAt: new DateTimeImmutable($model->created_at),
                    updatedAt: new DateTimeImmutable($model->updated_at),
                );
            })
            ->toArray();
    }

    /**
     * キーワード検索
     *
     * @param string|null $keyword 検索キーワード
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<int, array<string, mixed>> 検索結果（配列形式）
     */
    public function search(?string $keyword, array $searchableColumns): array
    {
        $query = AnxietyDiaryModel::query();

        // キーワード検索
        if ($keyword !== null && $keyword !== '' && count($searchableColumns) > 0) {
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

        return $query->orderByDesc('created_at')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'situation' => $item->situation,
                    'anxiety_thought' => $item->anxiety_thought,
                    'actual_outcome' => $item->actual_outcome,
                    'stressor_and_response_id' => $item->stressor_and_response_id,
                    'created_at' => $item->created_at->format(DATE_ATOM),
                    'updated_at' => $item->updated_at->format(DATE_ATOM),
                ];
            })
            ->toArray();
    }
}
