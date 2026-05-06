<?php

namespace App\Domain\Repository;

use App\Domain\Entity\EarlyMaladaptiveSchema;

interface EarlyMaladaptiveSchemaRepositoryInterface
{
    public function saveForMember(EarlyMaladaptiveSchema $schema, int $memberId): EarlyMaladaptiveSchema;

    public function findByIdForMember(int $id, int $memberId): ?EarlyMaladaptiveSchema;

    public function findFirstForMember(int $memberId): ?EarlyMaladaptiveSchema;

}
