<?php

namespace App\Domain\Repository;

use App\Domain\Entity\SupportNetwork;

interface SupportNetworkRepositoryInterface
{
    public function findAllForMember(int $memberId): array;

    public function saveForMember(SupportNetwork $supportNetwork, int $memberId): SupportNetwork;

    public function findByIdForMember(int $id, int $memberId): ?SupportNetwork;

    public function deleteForMember(int $id, int $memberId): void;
}
