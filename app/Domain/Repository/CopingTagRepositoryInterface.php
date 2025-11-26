<?php

namespace App\Domain\Repository;

use App\Domain\Entity\CopingTag;

interface CopingTagRepositoryInterface
{
    public function save(CopingTag $copingTag): CopingTag;

    public function findById(int $id): ?CopingTag;

    public function delete(int $id): void;
}
