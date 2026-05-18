<?php

namespace App\Application\UseCase\SchemaModeMonitoring;

use App\Application\DTO\SchemaModeMonitoringData;
use App\Domain\Entity\SchemaModeMonitoring as SchemaModeMonitoringEntity;
use App\Domain\Repository\SchemaModeMonitoringRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class UpdateSchemaModeMonitoringUseCase
{
    public function __construct(private readonly SchemaModeMonitoringRepositoryInterface $schemaModeMonitoringRepository)
    {
    }

    public function handle(int $id, SchemaModeMonitoringData $data): SchemaModeMonitoringEntity
    {
        $memberId = (int) Auth::id();
        $schemaModeMonitoring = $this->schemaModeMonitoringRepository->findByIdForMember($id, $memberId);

        if ($schemaModeMonitoring === null) {
            throw new DomainException('Schema mode monitoring not found.');
        }

        $updatedSchemaModeMonitoring = $schemaModeMonitoring->update($data->content);
        return $this->schemaModeMonitoringRepository->saveForMember($updatedSchemaModeMonitoring, $memberId);
    }
}
