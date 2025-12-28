<?php

namespace App\Domain\Repository;

use App\Domain\Entity\SupportNetwork;

interface SupportNetworkRepositoryInterface
{
    public function save(SupportNetwork $supportNetwork): SupportNetwork;

    public function findById(int $id): ?SupportNetwork;

    public function delete(int $id): void;
}
