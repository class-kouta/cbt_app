<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Chronology;

interface ChronologyRepositoryInterface
{
    public function save(Chronology $chronology): Chronology;

    public function findById(int $id): ?Chronology;

    public function delete(int $id): void;
}
