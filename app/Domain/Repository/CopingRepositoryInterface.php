<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Coping;

interface CopingRepositoryInterface
{
    public function save(Coping $coping): Coping;

    public function findById(int $id): ?Coping;

    public function delete(int $id): void;
}
