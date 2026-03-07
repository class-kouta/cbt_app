<?php

namespace App\Application\UseCase\SchemaModeMonitoring;

use App\Application\DTO\SchemaModeMonitoringData;
use App\Domain\Entity\SchemaModeMonitoring as SchemaModeMonitoringEntity;
use App\Domain\Repository\SchemaModeMonitoringRepositoryInterface;
use DomainException;

class UpdateSchemaModeMonitoringUseCase
{
    public function __construct(private readonly SchemaModeMonitoringRepositoryInterface $schemaModeMonitoringRepository)
    {
    }

    public function handle(int $id, SchemaModeMonitoringData $data): SchemaModeMonitoringEntity
    {
        $schemaModeMonitoring = $this->schemaModeMonitoringRepository->findById($id);

        if ($schemaModeMonitoring === null) {
            throw new DomainException('Schema mode monitoring not found.');
        }

        $updatedSchemaModeMonitoring = $schemaModeMonitoring->update($data->content);
        return $this->schemaModeMonitoringRepository->save($updatedSchemaModeMonitoring);
    }
}
