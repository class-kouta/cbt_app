<?php

namespace App\Domain\Repository;

use App\Domain\Entity\HappySchemaActionPlan;

interface HappySchemaActionPlanRepositoryInterface
{
    public function saveForMember(HappySchemaActionPlan $plan, int $memberId): HappySchemaActionPlan;

    public function findByIdForMember(int $id, int $memberId): ?HappySchemaActionPlan;

    public function findFirstForMember(int $memberId): ?HappySchemaActionPlan;

    /** @return HappySchemaActionPlan[] */
    public function findAllForMemberOrderedByLatest(int $memberId): array;
}
