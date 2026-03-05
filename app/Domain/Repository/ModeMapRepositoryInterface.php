<?php

namespace App\Domain\Repository;

use App\Domain\Entity\ModeMap;

interface ModeMapRepositoryInterface
{
    public function save(ModeMap $modeMap): ModeMap;

    public function findById(int $id): ?ModeMap;

    public function findFirst(): ?ModeMap;
}
