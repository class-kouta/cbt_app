<?php

namespace App\Application\UseCase\SchemaModeMonitoring;

use App\Domain\Repository\SchemaModeMonitoringRepositoryInterface;

class ListSchemaModeMonitoringsUseCase
{
    public function __construct(
        private readonly SchemaModeMonitoringRepositoryInterface $schemaModeMonitoringRepository
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function handle(): array
    {
        return $this->schemaModeMonitoringRepository->findAllOrderByCreatedAtDesc();
    }
}
