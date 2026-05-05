<?php

namespace App\Application\UseCase\SchemaModeMonitoring;

use App\Domain\Repository\SchemaModeMonitoringRepositoryInterface;
use Illuminate\Support\Facades\Auth;

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
        return $this->schemaModeMonitoringRepository->findAllForMemberOrderByCreatedAtDesc((int) Auth::id());
    }
}
