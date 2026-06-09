<?php

namespace App\Infrastructure\Repository;

use App\Application\DTO\SearchCriteriaData;
use App\Application\DTO\SessionSearchCriteriaData;
use App\Domain\Entity\Exposure as ExposureEntity;
use App\Domain\Entity\ExposureHierarchyItem as ExposureHierarchyItemEntity;
use App\Domain\Entity\ExposureSession as ExposureSessionEntity;
use App\Domain\Repository\ExposureRepositoryInterface;
use App\Infrastructure\Database\Models\Exposure as ExposureModel;
use App\Infrastructure\Database\Models\ExposureHierarchyItem as ExposureHierarchyItemModel;
use App\Infrastructure\Database\Models\ExposureSession as ExposureSessionModel;
use App\Support\LikeSearch;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class EloquentExposureRepository implements ExposureRepositoryInterface
{
    public function saveForMember(ExposureEntity $exposure, int $memberId): ExposureEntity
    {
        if ($exposure->getId() !== null) {
            $model = ExposureModel::where('member_id', $memberId)->findOrFail($exposure->getId());
        } else {
            $model = new ExposureModel();
        }

        $model->avoidance_target = $exposure->getAvoidanceTarget();
        $model->exposure_type = $exposure->getExposureType();
        $model->self_talk = $exposure->getSelfTalk();
        $model->overall_reflection = $exposure->getOverallReflection();
        $model->next_goal = $exposure->getNextGoal();
        $model->member_id = $memberId;
        $model->save();

        $model->load(['hierarchyItems', 'sessions']);

        return $this->toEntity($model);
    }

    public function findByIdForMember(int $id, int $memberId): ?ExposureEntity
    {
        $model = ExposureModel::with(['hierarchyItems', 'sessions'])->where('member_id', $memberId)->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function deleteForMember(int $id, int $memberId): void
    {
        $model = ExposureModel::where('member_id', $memberId)->find($id);

        if ($model !== null) {
            $model->delete();
        }
    }

    public function saveHierarchyItemForMember(int $exposureId, ExposureHierarchyItemEntity $item, int $memberId): ExposureHierarchyItemEntity
    {
        $ownerScopedExposure = ExposureModel::where('member_id', $memberId)->findOrFail($exposureId);

        $model = new ExposureHierarchyItemModel();
        $model->exposure_id = $ownerScopedExposure->id;
        $model->content = $item->getContent();
        $model->expected_suds = $item->getExpectedSuds();
        $model->sort_order = $item->getSortOrder();
        $model->save();

        return $this->toHierarchyItemEntity($model);
    }

    public function findHierarchyItemByIdForMember(int $itemId, int $memberId): ?ExposureHierarchyItemEntity
    {
        $model = ExposureHierarchyItemModel::whereHas('exposure', fn ($q) => $q->where('member_id', $memberId))->find($itemId);

        if ($model === null) {
            return null;
        }

        return $this->toHierarchyItemEntity($model);
    }

    public function updateHierarchyItemForMember(ExposureHierarchyItemEntity $item, int $memberId): ExposureHierarchyItemEntity
    {
        $model = ExposureHierarchyItemModel::whereHas('exposure', fn ($q) => $q->where('member_id', $memberId))->findOrFail($item->getId());
        $model->content = $item->getContent();
        $model->expected_suds = $item->getExpectedSuds();
        $model->sort_order = $item->getSortOrder();
        $model->save();

        return $this->toHierarchyItemEntity($model);
    }

    public function deleteHierarchyItemForMember(int $itemId, int $memberId): void
    {
        $model = ExposureHierarchyItemModel::whereHas('exposure', fn ($q) => $q->where('member_id', $memberId))->find($itemId);

        if ($model !== null) {
            $model->delete();
        }
    }

    public function searchSessionsForMember(SessionSearchCriteriaData $criteria, array $searchableColumns, int $memberId): array
    {
        $query = ExposureSessionModel::with(['exposure', 'hierarchyItem'])
            ->whereNotNull('action_plan')
            ->where('action_plan', '!=', '')
            ->whereHas('exposure', fn ($q) => $q->where('member_id', $memberId));

        if ($criteria->hasKeyword() && count($searchableColumns) > 0) {
            $pattern = LikeSearch::containsPattern($criteria->keyword);
            $query->where(function ($q) use ($pattern, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere("exposure_sessions.{$column}", 'like', $pattern);
                }
                $q->orWhereHas('exposure', function ($subQ) use ($pattern) {
                    $subQ->where('avoidance_target', 'like', $pattern);
                });
            });
        }

        if ($criteria->filter === 'pending') {
            $query->where(function ($q) {
                $q->whereNull('reflection')->orWhere('reflection', '');
            });
        } elseif ($criteria->filter === 'completed') {
            $query->whereNotNull('reflection')->where('reflection', '!=', '');
        }

        $paginator = $query->orderByDesc('created_at')
            ->paginate($criteria->perPage, ['*'], 'page', $criteria->page);

        return [
            'data' => collect($paginator->items())->map(function ($session) {
                return [
                    'id' => $session->id,
                    'exposure_id' => $session->exposure_id,
                    'avoidance_target' => $session->exposure->avoidance_target ?? '',
                    'hierarchy_item_id' => $session->hierarchy_item_id,
                    'hierarchy_item_content' => $session->hierarchyItem->content ?? '',
                    'session_number' => $session->session_number,
                    'action_plan' => $session->action_plan,
                    'suds_before' => $session->suds_before,
                    'suds_peak' => $session->suds_peak,
                    'suds_after' => $session->suds_after,
                    'performed_at' => $session->performed_at?->format(DATE_ATOM),
                    'reflection' => $session->reflection,
                    'created_at' => $session->created_at->format(DATE_ATOM),
                    'updated_at' => $session->updated_at->format(DATE_ATOM),
                ];
            })->toArray(),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }

    public function saveSessionForMember(int $exposureId, ExposureSessionEntity $session, int $memberId): ExposureSessionEntity
    {
        $ownerScopedExposure = ExposureModel::where('member_id', $memberId)->findOrFail($exposureId);

        $model = new ExposureSessionModel();
        $model->exposure_id = $ownerScopedExposure->id;
        $model->hierarchy_item_id = $session->getHierarchyItemId();
        $model->session_number = $session->getSessionNumber();
        $model->action_plan = $session->getActionPlan();
        $model->suds_before = $session->getSudsBefore();
        $model->suds_peak = $session->getSudsPeak();
        $model->suds_after = $session->getSudsAfter();
        $model->performed_at = $session->getPerformedAt();
        $model->reflection = $session->getReflection();
        $model->save();

        return $this->toSessionEntity($model);
    }

    public function findSessionByIdForMember(int $sessionId, int $memberId): ?ExposureSessionEntity
    {
        $model = ExposureSessionModel::whereHas('exposure', fn ($q) => $q->where('member_id', $memberId))->find($sessionId);

        if ($model === null) {
            return null;
        }

        return $this->toSessionEntity($model);
    }

    public function updateSessionForMember(ExposureSessionEntity $session, int $memberId): ExposureSessionEntity
    {
        $model = ExposureSessionModel::whereHas('exposure', fn ($q) => $q->where('member_id', $memberId))->findOrFail($session->getId());
        $model->hierarchy_item_id = $session->getHierarchyItemId();
        $model->action_plan = $session->getActionPlan();
        $model->suds_before = $session->getSudsBefore();
        $model->suds_peak = $session->getSudsPeak();
        $model->suds_after = $session->getSudsAfter();
        $model->performed_at = $session->getPerformedAt();
        $model->reflection = $session->getReflection();
        $model->save();

        return $this->toSessionEntity($model);
    }

    public function deleteSessionForMember(int $sessionId, int $memberId): void
    {
        $model = ExposureSessionModel::whereHas('exposure', fn ($q) => $q->where('member_id', $memberId))->find($sessionId);

        if ($model !== null) {
            $model->delete();
        }
    }

    /**
     * @param ExposureHierarchyItemEntity[] $items
     * @return ExposureHierarchyItemEntity[]
     */
    public function syncHierarchyItemsForMember(int $exposureId, array $items, int $memberId): array
    {
        return DB::transaction(function () use ($exposureId, $items, $memberId) {
            $exposure = ExposureModel::where('member_id', $memberId)->findOrFail($exposureId);

            $keepIds = array_values(array_filter(array_map(
                fn (ExposureHierarchyItemEntity $item) => $item->getId(),
                $items
            )));

            $deleteQuery = ExposureHierarchyItemModel::where('exposure_id', $exposure->id);
            if (count($keepIds) > 0) {
                $deleteQuery->whereNotIn('id', $keepIds);
            }
            $deleteQuery->delete();

            $entities = [];
            foreach ($items as $item) {
                if ($item->getId() !== null) {
                    $model = ExposureHierarchyItemModel::where('exposure_id', $exposure->id)->findOrFail($item->getId());
                } else {
                    $model = new ExposureHierarchyItemModel();
                    $model->exposure_id = $exposure->id;
                }

                $model->content = $item->getContent();
                $model->expected_suds = $item->getExpectedSuds();
                $model->sort_order = $item->getSortOrder();
                $model->save();
                $entities[] = $this->toHierarchyItemEntity($model);
            }

            return $entities;
        });
    }

    /**
     * @param ExposureSessionEntity[] $sessions
     * @return ExposureSessionEntity[]
     */
    public function syncSessionsForMember(int $exposureId, array $sessions, int $memberId): array
    {
        return DB::transaction(function () use ($exposureId, $sessions, $memberId) {
            $exposure = ExposureModel::where('member_id', $memberId)->findOrFail($exposureId);

            $keepIds = array_values(array_filter(array_map(
                fn (ExposureSessionEntity $session) => $session->getId(),
                $sessions
            )));

            $deleteQuery = ExposureSessionModel::where('exposure_id', $exposure->id);
            if (count($keepIds) > 0) {
                $deleteQuery->whereNotIn('id', $keepIds);
            }
            $deleteQuery->delete();

            $entities = [];
            foreach ($sessions as $session) {
                if ($session->getId() !== null) {
                    $model = ExposureSessionModel::where('exposure_id', $exposure->id)->findOrFail($session->getId());
                } else {
                    $model = new ExposureSessionModel();
                    $model->exposure_id = $exposure->id;
                }

                $model->session_number = $session->getSessionNumber();
                $model->hierarchy_item_id = $session->getHierarchyItemId();
                $model->action_plan = $session->getActionPlan();
                $model->suds_before = $session->getSudsBefore();
                $model->suds_peak = $session->getSudsPeak();
                $model->suds_after = $session->getSudsAfter();
                $model->performed_at = $session->getPerformedAt();
                $model->reflection = $session->getReflection();
                $model->save();

                $entities[] = $this->toSessionEntity($model);
            }

            return $entities;
        });
    }

    public function hierarchyItemBelongsToExposureForMember(int $hierarchyItemId, int $exposureId, int $memberId): bool
    {
        return ExposureHierarchyItemModel::where('id', $hierarchyItemId)
            ->where('exposure_id', $exposureId)
            ->whereHas('exposure', fn ($q) => $q->where('member_id', $memberId))
            ->exists();
    }

    public function searchForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): array
    {
        $query = ExposureModel::with(['hierarchyItems', 'sessions'])->where('member_id', $memberId);

        if ($criteria->hasKeyword() && count($searchableColumns) > 0) {
            $pattern = LikeSearch::containsPattern($criteria->keyword);
            $query->where(function ($q) use ($pattern, $searchableColumns) {
                foreach ($searchableColumns as $index => $column) {
                    if ($index === 0) {
                        $q->where($column, 'like', $pattern);
                    } else {
                        $q->orWhere($column, 'like', $pattern);
                    }
                }
            });
        }

        $paginator = $query->orderByDesc('created_at')
            ->paginate($criteria->perPage, ['*'], 'page', $criteria->page);

        $items = collect($paginator->items())
            ->map(fn ($exposure) => $this->formatExposureArray($exposure))
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

    public function searchAllForMember(SearchCriteriaData $criteria, array $searchableColumns, int $memberId): array
    {
        $query = ExposureModel::with(['hierarchyItems', 'sessions'])->where('member_id', $memberId);

        if ($criteria->hasKeyword() && count($searchableColumns) > 0) {
            $pattern = LikeSearch::containsPattern($criteria->keyword);
            $query->where(function ($q) use ($pattern, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', $pattern);
                }
            });
        }

        return $query->orderByDesc('created_at')
            ->get()
            ->map(fn ($exposure) => $this->formatExposureArray($exposure))
            ->toArray();
    }

    private function toEntity(ExposureModel $model): ExposureEntity
    {
        $hierarchyItems = $model->hierarchyItems->map(fn ($item) => $this->toHierarchyItemEntity($item))->toArray();
        $sessions = $model->sessions->map(fn ($session) => $this->toSessionEntity($session))->toArray();

        return ExposureEntity::reconstitute(
            id: (int) $model->id,
            avoidanceTarget: (string) $model->avoidance_target,
            exposureType: $model->exposure_type,
            selfTalk: $model->self_talk,
            overallReflection: $model->overall_reflection,
            nextGoal: $model->next_goal,
            hierarchyItems: $hierarchyItems,
            sessions: $sessions,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    private function toHierarchyItemEntity(ExposureHierarchyItemModel $model): ExposureHierarchyItemEntity
    {
        return ExposureHierarchyItemEntity::reconstitute(
            id: (int) $model->id,
            exposureId: (int) $model->exposure_id,
            content: (string) $model->content,
            expectedSuds: $model->expected_suds,
            sortOrder: (int) $model->sort_order,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    private function toSessionEntity(ExposureSessionModel $model): ExposureSessionEntity
    {
        return ExposureSessionEntity::reconstitute(
            id: (int) $model->id,
            exposureId: (int) $model->exposure_id,
            hierarchyItemId: $model->hierarchy_item_id,
            sessionNumber: (int) $model->session_number,
            actionPlan: $model->action_plan,
            sudsBefore: $model->suds_before,
            sudsPeak: $model->suds_peak,
            sudsAfter: $model->suds_after,
            performedAt: $model->performed_at ? DateTimeImmutable::createFromInterface($model->performed_at) : null,
            reflection: $model->reflection,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function formatExposureArray(ExposureModel $exposure): array
    {
        return [
            'id' => $exposure->id,
            'avoidance_target' => $exposure->avoidance_target,
            'self_talk' => $exposure->self_talk,
            'overall_reflection' => $exposure->overall_reflection,
            'next_goal' => $exposure->next_goal,
            'hierarchy_items' => $exposure->hierarchyItems->map(fn ($item) => [
                'id' => $item->id,
                'content' => $item->content,
                'expected_suds' => $item->expected_suds,
                'sort_order' => $item->sort_order,
            ])->toArray(),
            'sessions' => $exposure->sessions->map(fn ($session) => [
                'id' => $session->id,
                'hierarchy_item_id' => $session->hierarchy_item_id,
                'session_number' => $session->session_number,
                'action_plan' => $session->action_plan,
                'suds_before' => $session->suds_before,
                'suds_peak' => $session->suds_peak,
                'suds_after' => $session->suds_after,
                'performed_at' => $session->performed_at?->format(DATE_ATOM),
                'reflection' => $session->reflection,
                'created_at' => $session->created_at->format(DATE_ATOM),
                'updated_at' => $session->updated_at->format(DATE_ATOM),
            ])->toArray(),
            'created_at' => $exposure->created_at->format(DATE_ATOM),
            'updated_at' => $exposure->updated_at->format(DATE_ATOM),
        ];
    }
}
