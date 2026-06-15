<?php

namespace App\Domain\Repository;

use App\Domain\Entity\StressPersonEncyclopedia;

interface StressPersonEncyclopediaRepositoryInterface
{
    /**
     * @return list<StressPersonEncyclopedia>
     */
    public function findAllForMember(int $memberId): array;

    public function saveForMember(StressPersonEncyclopedia $encyclopedia, int $memberId): StressPersonEncyclopedia;

    public function findByIdForMember(int $id, int $memberId): ?StressPersonEncyclopedia;

    public function deleteForMember(int $id, int $memberId): void;
}
