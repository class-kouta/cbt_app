<?php

namespace App\Infrastructure\Repository;

use App\Application\DTO\ConditionCheckSearchCriteriaData;
use App\Domain\Entity\ConditionCheck as ConditionCheckEntity;
use App\Domain\Repository\ConditionCheckRepositoryInterface;
use App\Infrastructure\Database\Models\ConditionCheck as ConditionCheckModel;

class EloquentConditionCheckRepository implements ConditionCheckRepositoryInterface
{
    private function toEntity(ConditionCheckModel $model): ConditionCheckEntity
    {
        return ConditionCheckEntity::reconstitute(
            id: (int) $model->id,
            mood: (int) $model->mood,
            fatigue: (int) $model->fatigue,
            anxiety: (int) $model->anxiety,
            sleepiness: (int) $model->sleepiness,
            physicalCondition: (int) $model->physical_condition,
            memo: $model->memo !== null ? (string) $model->memo : null,
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function toArray(ConditionCheckEntity $conditionCheck): array
    {
        return [
            'id' => $conditionCheck->getId(),
            'mood' => $conditionCheck->getMood(),
            'fatigue' => $conditionCheck->getFatigue(),
            'anxiety' => $conditionCheck->getAnxiety(),
            'sleepiness' => $conditionCheck->getSleepiness(),
            'physical_condition' => $conditionCheck->getPhysicalCondition(),
            'memo' => $conditionCheck->getMemo(),
            'created_at' => $conditionCheck->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $conditionCheck->getUpdatedAt()->format(DATE_ATOM),
        ];
    }

    public function searchForMember(ConditionCheckSearchCriteriaData $criteria, int $memberId): array
    {
        $paginator = ConditionCheckModel::where('member_id', $memberId)
            ->orderByDesc('created_at')
            ->paginate($criteria->perPage, ['*'], 'page', $criteria->page);

        $items = collect($paginator->items())
            ->map(fn ($model) => $this->toArray($this->toEntity($model)))
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

    public function saveForMember(ConditionCheckEntity $conditionCheck, int $memberId): ConditionCheckEntity
    {
        if ($conditionCheck->getId() !== null) {
            $model = ConditionCheckModel::where('member_id', $memberId)
                ->findOrFail($conditionCheck->getId());
        } else {
            $model = new ConditionCheckModel();
            $model->member_id = $memberId;
        }

        $model->mood = $conditionCheck->getMood();
        $model->fatigue = $conditionCheck->getFatigue();
        $model->anxiety = $conditionCheck->getAnxiety();
        $model->sleepiness = $conditionCheck->getSleepiness();
        $model->physical_condition = $conditionCheck->getPhysicalCondition();
        $model->memo = $conditionCheck->getMemo();
        $model->save();

        return $this->toEntity($model);
    }

    public function findByIdForMember(int $id, int $memberId): ?ConditionCheckEntity
    {
        $model = ConditionCheckModel::where('member_id', $memberId)->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function deleteForMember(int $id, int $memberId): void
    {
        $model = ConditionCheckModel::where('member_id', $memberId)->find($id);

        if ($model !== null) {
            $model->delete();
        }
    }
}
