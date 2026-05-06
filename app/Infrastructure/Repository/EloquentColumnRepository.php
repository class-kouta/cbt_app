<?php

namespace App\Infrastructure\Repository;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Entity\Column as ColumnEntity;
use App\Domain\Repository\ColumnRepositoryInterface;
use App\Infrastructure\Database\Models\Column as ColumnModel;
use DateTimeImmutable;

class EloquentColumnRepository implements ColumnRepositoryInterface
{
    public function saveForMember(ColumnEntity $column, int $memberId): ColumnEntity
    {
        if ($column->getId() !== null) {
            // 更新
            $model = ColumnModel::where('member_id', $memberId)->findOrFail($column->getId());
            $model->situation = $column->getSituation();
            $model->mood = $column->getMood();
            $model->automatic_thought = $column->getAutomaticThought();
            $model->evidence = $column->getEvidence();
            $model->counter_evidence = $column->getCounterEvidence();
            $model->adaptive_thought = $column->getAdaptiveThought();
            $model->current_mood = $column->getCurrentMood();
            $model->notes = $column->getNotes();
            $model->stressor_and_response_id = $column->getStressorAndResponseId();
            $model->member_id = $memberId;
            $model->save();
        } else {
            // 新規作成
            $model = new ColumnModel();
            $model->situation = $column->getSituation();
            $model->mood = $column->getMood();
            $model->automatic_thought = $column->getAutomaticThought();
            $model->evidence = $column->getEvidence();
            $model->counter_evidence = $column->getCounterEvidence();
            $model->adaptive_thought = $column->getAdaptiveThought();
            $model->current_mood = $column->getCurrentMood();
            $model->notes = $column->getNotes();
            $model->stressor_and_response_id = $column->getStressorAndResponseId();
            $model->member_id = $memberId;
            $model->save();
        }

        return ColumnEntity::reconstitute(
            id: (int) $model->getKey(),
            situation: (string) $model->situation,
            mood: $model->mood,
            automaticThought: $model->automatic_thought,
            evidence: $model->evidence,
            counterEvidence: $model->counter_evidence,
            adaptiveThought: $model->adaptive_thought,
            currentMood: $model->current_mood,
            notes: $model->notes,
            stressorAndResponseId: $model->stressor_and_response_id,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    /**
     * コラムを保存し、タグを同期する
     *
     * @param ColumnEntity $column コラムエンティティ
     * @param array<int> $tagIds タグIDの配列
     * @return array<string, mixed> 保存結果（タグ情報を含む）
     */
    public function saveWithTagsForMember(ColumnEntity $column, array $tagIds, int $memberId): array
    {
        // コラムを保存
        $savedColumn = $this->saveForMember($column, $memberId);

        // タグを同期
        $model = ColumnModel::with('tags')->where('member_id', $memberId)->findOrFail($savedColumn->getId());
        $model->tags()->sync($tagIds);
        $model->load('tags');

        return [
            'id' => $savedColumn->getId(),
            'situation' => $savedColumn->getSituation(),
            'mood' => $savedColumn->getMood(),
            'automatic_thought' => $savedColumn->getAutomaticThought(),
            'evidence' => $savedColumn->getEvidence(),
            'counter_evidence' => $savedColumn->getCounterEvidence(),
            'adaptive_thought' => $savedColumn->getAdaptiveThought(),
            'current_mood' => $savedColumn->getCurrentMood(),
            'notes' => $savedColumn->getNotes(),
            'stressor_and_response_id' => $savedColumn->getStressorAndResponseId(),
            'tags' => $model->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])->toArray(),
            'tag_ids' => $model->tags->pluck('id')->toArray(),
            'created_at' => $savedColumn->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $savedColumn->getUpdatedAt()->format(DATE_ATOM),
        ];
    }

    public function findByIdForMember(int $id, int $memberId): ?ColumnEntity
    {
        $model = ColumnModel::where('member_id', $memberId)->find($id);

        if ($model === null) {
            return null;
        }

        return ColumnEntity::reconstitute(
            id: (int) $model->id,
            situation: (string) $model->situation,
            mood: $model->mood,
            automaticThought: $model->automatic_thought,
            evidence: $model->evidence,
            counterEvidence: $model->counter_evidence,
            adaptiveThought: $model->adaptive_thought,
            currentMood: $model->current_mood,
            notes: $model->notes,
            stressorAndResponseId: $model->stressor_and_response_id,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    public function deleteForMember(int $id, int $memberId): void
    {
        $model = ColumnModel::where('member_id', $memberId)->find($id);

        if ($model !== null) {
            $model->delete();
        }
    }

    /**
     * 検索条件に基づいてコラムを検索（ページネーション対応）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<string, mixed> 検索結果（ページネーション情報を含む）
     */
    public function searchForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): array
    {
        $query = ColumnModel::with('tags')->where('member_id', $memberId);

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

        $paginator = $query->orderByDesc('created_at')
            ->paginate($criteria->perPage, ['*'], 'page', $criteria->page);

        $items = collect($paginator->items())
            ->map(function ($column) {
                return [
                    'id' => $column->id,
                    'situation' => $column->situation,
                    'mood' => $column->mood,
                    'automatic_thought' => $column->automatic_thought,
                    'evidence' => $column->evidence,
                    'counter_evidence' => $column->counter_evidence,
                    'adaptive_thought' => $column->adaptive_thought,
                    'current_mood' => $column->current_mood,
                    'notes' => $column->notes,
                    'stressor_and_response_id' => $column->stressor_and_response_id,
                    'tags' => $column->tags->map(fn ($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ])->toArray(),
                    'created_at' => $column->created_at->format(DATE_ATOM),
                    'updated_at' => $column->updated_at->format(DATE_ATOM),
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
     * 検索条件に基づいてコラムを全件取得（CSV出力用）
     *
     * @param SearchCriteriaData $criteria 検索条件
     * @param array<int, string> $searchableColumns キーワード検索対象カラム
     * @return array<int, array<string, mixed>> 検索結果
     */
    public function searchAllForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): array
    {
        $query = ColumnModel::with('tags')->where('member_id', $memberId);

        // キーワード検索
        if ($criteria->hasKeyword() && count($searchableColumns) > 0) {
            $keyword = $criteria->keyword;
            $query->where(function ($q) use ($keyword, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$keyword}%");
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
            ->map(function ($column) {
                return [
                    'id' => $column->id,
                    'situation' => $column->situation,
                    'mood' => $column->mood,
                    'automatic_thought' => $column->automatic_thought,
                    'evidence' => $column->evidence,
                    'counter_evidence' => $column->counter_evidence,
                    'adaptive_thought' => $column->adaptive_thought,
                    'current_mood' => $column->current_mood,
                    'notes' => $column->notes,
                    'stressor_and_response_id' => $column->stressor_and_response_id,
                    'tags' => $column->tags->map(fn ($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ])->toArray(),
                    'created_at' => $column->created_at->format(DATE_ATOM),
                    'updated_at' => $column->updated_at->format(DATE_ATOM),
                ];
            })
            ->toArray();
    }
}
