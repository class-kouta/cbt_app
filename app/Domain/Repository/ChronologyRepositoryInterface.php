<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Chronology;

interface ChronologyRepositoryInterface
{
    public function findAllForMember(int $memberId): array;

    public function saveForMember(Chronology $chronology, int $memberId): Chronology;

    public function findByIdForMember(int $id, int $memberId): ?Chronology;

    public function deleteForMember(int $id, int $memberId): void;
}
