<?php

namespace App\Domain\Repository;

use App\Domain\Entity\SchemaModeMonitoring;

interface SchemaModeMonitoringRepositoryInterface
{
    public function saveForMember(SchemaModeMonitoring $schemaModeMonitoring, int $memberId): SchemaModeMonitoring;

    public function findByIdForMember(int $id, int $memberId): ?SchemaModeMonitoring;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllForMemberOrderByCreatedAtDesc(int $memberId): array;

    public function deleteForMember(int $id, int $memberId): void;
}
