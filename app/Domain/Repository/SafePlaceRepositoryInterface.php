<?php

namespace App\Domain\Repository;

use App\Domain\Entity\SafePlace;

interface SafePlaceRepositoryInterface
{
    public function save(SafePlace $safePlace): SafePlace;

    public function findById(int $id): ?SafePlace;

    public function findFirst(): ?SafePlace;
}
