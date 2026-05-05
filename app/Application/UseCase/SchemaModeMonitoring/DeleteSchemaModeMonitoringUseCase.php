<?php

namespace App\Application\UseCase\SchemaModeMonitoring;

use App\Domain\Repository\SchemaModeMonitoringRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DeleteSchemaModeMonitoringUseCase
{
    public function __construct(private readonly SchemaModeMonitoringRepositoryInterface $schemaModeMonitoringRepository)
    {
    }

    public function handle(int $id): void
    {
        $this->schemaModeMonitoringRepository->deleteForMember($id, (int) Auth::id());
    }
}
