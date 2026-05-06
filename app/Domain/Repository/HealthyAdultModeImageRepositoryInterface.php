<?php

namespace App\Domain\Repository;

use App\Domain\Entity\HealthyAdultModeImage;

interface HealthyAdultModeImageRepositoryInterface
{
    public function saveForMember(HealthyAdultModeImage $entity, int $memberId): HealthyAdultModeImage;

    public function findByIdForMember(int $id, int $memberId): ?HealthyAdultModeImage;

    public function findFirstForMember(int $memberId): ?HealthyAdultModeImage;
}
