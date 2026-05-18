<?php

namespace App\Domain\Repository;

use App\Domain\Entity\ModeMap;

interface ModeMapRepositoryInterface
{
    public function saveForMember(ModeMap $modeMap, int $memberId): ModeMap;

    public function findByIdForMember(int $id, int $memberId): ?ModeMap;

    public function findFirstForMember(int $memberId): ?ModeMap;
}
