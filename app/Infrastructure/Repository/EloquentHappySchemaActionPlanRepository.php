<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\HappySchemaActionPlan as HappySchemaActionPlanEntity;
use App\Domain\Repository\HappySchemaActionPlanRepositoryInterface;
use App\Infrastructure\Database\Models\HappySchemaActionPlan as HappySchemaActionPlanModel;
use DateTimeImmutable;

class EloquentHappySchemaActionPlanRepository implements HappySchemaActionPlanRepositoryInterface
{
    public function saveForMember(HappySchemaActionPlanEntity $plan, int $memberId): HappySchemaActionPlanEntity
    {
        $model = HappySchemaActionPlanModel::updateOrCreate(
            ['member_id' => $memberId],
            [
                'happy_schema' => $plan->getHappySchema(),
                'action_plan' => $plan->getActionPlan(),
            ]
        );

        return $this->toEntity($model);
    }

    public function findByIdForMember(int $id, int $memberId): ?HappySchemaActionPlanEntity
    {
        $model = HappySchemaActionPlanModel::where('member_id', $memberId)->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findFirstForMember(int $memberId): ?HappySchemaActionPlanEntity
    {
        $model = HappySchemaActionPlanModel::where('member_id', $memberId)->first();

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    /** @return HappySchemaActionPlanEntity[] */
    public function findAllForMemberOrderedByLatest(int $memberId): array
    {
        return HappySchemaActionPlanModel::where('member_id', $memberId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($model) => $this->toEntity($model))
            ->all();
    }

    private function toEntity(HappySchemaActionPlanModel $model): HappySchemaActionPlanEntity
    {
        return HappySchemaActionPlanEntity::reconstitute(
            id: (int) $model->getKey(),
            happySchema: $model->happy_schema,
            actionPlan: $model->action_plan,
            createdAt: new DateTimeImmutable($model->created_at),
            updatedAt: new DateTimeImmutable($model->updated_at),
        );
    }
}
