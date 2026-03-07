<?php

namespace App\Domain\Repository;

use App\Domain\Entity\SchemaModeMonitoring;

interface SchemaModeMonitoringRepositoryInterface
{
    public function save(SchemaModeMonitoring $schemaModeMonitoring): SchemaModeMonitoring;

    public function findById(int $id): ?SchemaModeMonitoring;

    public function delete(int $id): void;
}
