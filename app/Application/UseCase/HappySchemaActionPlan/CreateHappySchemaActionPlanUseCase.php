<?php

namespace App\Application\UseCase\HappySchemaActionPlan;

use App\Application\DTO\HappySchemaActionPlanData;
use App\Domain\Entity\HappySchemaActionPlan as HappySchemaActionPlanEntity;
use App\Domain\Repository\HappySchemaActionPlanRepositoryInterface;

class CreateHappySchemaActionPlanUseCase
{
    public function __construct(private readonly HappySchemaActionPlanRepositoryInterface $repository) {}

    public function handle(HappySchemaActionPlanData $data): HappySchemaActionPlanEntity
    {
        $plan = HappySchemaActionPlanEntity::createNew(
            $data->happySchema,
            $data->actionPlan
        );

        return $this->repository->save($plan);
    }
}
