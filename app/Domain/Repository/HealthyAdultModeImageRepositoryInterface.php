<?php

namespace App\Domain\Repository;

use App\Domain\Entity\HealthyAdultModeImage;

interface HealthyAdultModeImageRepositoryInterface
{
    public function save(HealthyAdultModeImage $entity): HealthyAdultModeImage;

    public function findById(int $id): ?HealthyAdultModeImage;

    public function findFirst(): ?HealthyAdultModeImage;
}
