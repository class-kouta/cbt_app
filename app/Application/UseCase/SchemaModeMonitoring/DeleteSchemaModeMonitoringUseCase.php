<?php

namespace App\Application\UseCase\SchemaModeMonitoring;

use App\Domain\Repository\SchemaModeMonitoringRepositoryInterface;

class DeleteSchemaModeMonitoringUseCase
{
    public function __construct(private readonly SchemaModeMonitoringRepositoryInterface $schemaModeMonitoringRepository)
    {
    }

    public function handle(int $id): void
    {
        $this->schemaModeMonitoringRepository->delete($id);
    }
}
