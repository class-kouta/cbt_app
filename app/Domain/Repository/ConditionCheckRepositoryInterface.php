<?php

namespace App\Domain\Repository;

use App\Domain\Entity\ConditionCheck;

interface ConditionCheckRepositoryInterface
{
    public function findAllForMember(int $memberId): array;

    public function saveForMember(ConditionCheck $conditionCheck, int $memberId): ConditionCheck;

    public function findByIdForMember(int $id, int $memberId): ?ConditionCheck;

    public function deleteForMember(int $id, int $memberId): void;
}
