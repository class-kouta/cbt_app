<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\HappySchemaActionPlan as HappySchemaActionPlanEntity;
use App\Domain\Repository\HappySchemaActionPlanRepositoryInterface;
use App\Infrastructure\Database\Models\HappySchemaActionPlan as HappySchemaActionPlanModel;
use DateTimeImmutable;

class EloquentHappySchemaActionPlanRepository implements HappySchemaActionPlanRepositoryInterface
{
    public function save(HappySchemaActionPlanEntity $plan): HappySchemaActionPlanEntity
    {
        $model = HappySchemaActionPlanModel::updateOrCreate(
            ['id' => $plan->getId()],
            [
                'happy_schema' => $plan->getHappySchema(),
                'action_plan' => $plan->getActionPlan(),
            ]
        );

        return $this->toEntity($model);
    }

    public function findById(int $id): ?HappySchemaActionPlanEntity
    {
        $model = HappySchemaActionPlanModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findFirst(): ?HappySchemaActionPlanEntity
    {
        $model = HappySchemaActionPlanModel::latest()->first();

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    /** @return HappySchemaActionPlanEntity[] */
    public function findAllOrderedByLatest(): array
    {
        return HappySchemaActionPlanModel::orderByDesc('created_at')
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
