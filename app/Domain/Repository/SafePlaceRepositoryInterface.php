<?php

namespace App\Domain\Repository;

use App\Domain\Entity\SafePlace;

interface SafePlaceRepositoryInterface
{
    public function saveForMember(SafePlace $safePlace, int $memberId): SafePlace;

    public function findByIdForMember(int $id, int $memberId): ?SafePlace;

    public function findFirstForMember(int $memberId): ?SafePlace;
}
