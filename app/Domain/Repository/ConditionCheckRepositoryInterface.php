<?php

namespace App\Domain\Repository;

use App\Application\DTO\ConditionCheckSearchCriteriaData;
use App\Domain\Entity\ConditionCheck;

interface ConditionCheckRepositoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function searchForMember(ConditionCheckSearchCriteriaData $criteria, int $memberId): array;

    public function saveForMember(ConditionCheck $conditionCheck, int $memberId): ConditionCheck;

    public function findByIdForMember(int $id, int $memberId): ?ConditionCheck;

    public function deleteForMember(int $id, int $memberId): void;
}
