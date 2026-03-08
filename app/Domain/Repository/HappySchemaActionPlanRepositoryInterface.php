<?php

namespace App\Domain\Repository;

use App\Domain\Entity\HappySchemaActionPlan;

interface HappySchemaActionPlanRepositoryInterface
{
    public function save(HappySchemaActionPlan $plan): HappySchemaActionPlan;

    public function findById(int $id): ?HappySchemaActionPlan;

    public function findFirst(): ?HappySchemaActionPlan;
}
