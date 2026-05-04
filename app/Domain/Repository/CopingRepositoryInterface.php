<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Coping;

interface CopingRepositoryInterface
{
    public function findAllForMember(int $memberId): array;

    public function saveForMember(Coping $coping, int $memberId): Coping;

    public function findByIdForMember(int $id, int $memberId): ?Coping;

    public function deleteForMember(int $id, int $memberId): void;
}
