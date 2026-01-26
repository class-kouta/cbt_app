<?php

namespace App\Infrastructure\Repository;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Entity\AnxietyDiary as AnxietyDiaryEntity;
use App\Domain\Repository\AnxietyDiaryRepositoryInterface;
use App\Infrastructure\Database\Models\AnxietyDiary as AnxietyDiaryModel;
use DateTimeImmutable;

class EloquentAnxietyDiaryRepository implements AnxietyDiaryRepositoryInterface
{
    public function save(AnxietyDiaryEntity $anxietyDiary): AnxietyDiaryEntity
    {
        $attributes = [
            'situation' => $anxietyDiary->getSituation(),
            'anxiety_thought' => $anxietyDiary->getAnxietyThought(),
            'actual_outcome' => $anxietyDiary->getActualOutcome(),
            'stressor_and_response_id' => $anxietyDiary->getStressorAndResponseId(),
        ];

        $model = AnxietyDiaryModel::updateOrCreate(
            ['id' => $anxietyDiary->getId()],
            $attributes
        );

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
        AnxietyDiaryModel::destroy($id);
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
     * 検索条件に基づいて不安日記を検索（ページネーション対応）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<string, mixed> 検索結果（ページネーション情報を含む）
     */
    public function search(SearchCriteriaData $criteria, array $searchableColumns): array
    {
        $query = AnxietyDiaryModel::query();

        // キーワード検索
        if ($criteria->hasKeyword() && count($searchableColumns) > 0) {
            $keyword = $criteria->keyword;
            $query->where(function ($q) use ($keyword, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$keyword}%");
                }
            });
        }

        $paginator = $query->orderByDesc('created_at')
            ->paginate($criteria->perPage, ['*'], 'page', $criteria->page);

        $items = collect($paginator->items())
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

        return [
            'data' => $items,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }

    /**
     * 検索条件に基づいて不安日記を全件取得（CSV出力用）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<int, array<string, mixed>> 検索結果
     */
    public function searchAll(SearchCriteriaData $criteria, array $searchableColumns): array
    {
        $query = AnxietyDiaryModel::query();

        // キーワード検索
        if ($criteria->hasKeyword() && count($searchableColumns) > 0) {
            $keyword = $criteria->keyword;
            $query->where(function ($q) use ($keyword, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$keyword}%");
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
