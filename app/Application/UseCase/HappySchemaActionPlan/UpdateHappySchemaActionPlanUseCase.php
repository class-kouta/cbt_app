<?php

namespace App\Application\UseCase\HappySchemaActionPlan;

use App\Application\DTO\HappySchemaActionPlanData;
use App\Domain\Entity\HappySchemaActionPlan as HappySchemaActionPlanEntity;
use App\Domain\Repository\HappySchemaActionPlanRepositoryInterface;

class UpdateHappySchemaActionPlanUseCase
{
    public function __construct(private readonly HappySchemaActionPlanRepositoryInterface $repository) {}

    public function handle(int $id, HappySchemaActionPlanData $data): HappySchemaActionPlanEntity
    {
        $existing = $this->repository->findById($id);

        if ($existing === null) {
            throw new \RuntimeException('ハッピースキーマと行動計画が見つかりません');
        }

        $plan = HappySchemaActionPlanEntity::reconstitute(
            $id,
            $data->happySchema,
            $data->actionPlan,
            $existing->getCreatedAt(),
            new \DateTimeImmutable('now')
        );

        return $this->repository->save($plan);
    }
}
