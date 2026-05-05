<?php

namespace App\Application\UseCase\SchemaModeMonitoring;

use App\Application\DTO\SchemaModeMonitoringData;
use App\Domain\Entity\SchemaModeMonitoring as SchemaModeMonitoringEntity;
use App\Domain\Repository\SchemaModeMonitoringRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateSchemaModeMonitoringUseCase
{
    public function __construct(private readonly SchemaModeMonitoringRepositoryInterface $schemaModeMonitoringRepository)
    {
    }

    public function handle(SchemaModeMonitoringData $data): SchemaModeMonitoringEntity
    {
        $schemaModeMonitoring = SchemaModeMonitoringEntity::createNew($data->content);
        return $this->schemaModeMonitoringRepository->saveForMember($schemaModeMonitoring, (int) Auth::id());
    }
}
