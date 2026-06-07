<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\SessionSearchCriteriaData;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchSessionUseCase
{
    private const SEARCHABLE_COLUMNS = [
        'action_plan',
        'reflection',
    ];

    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(SessionSearchCriteriaData $criteria): array
    {
        return $this->repository->searchSessionsForMember($criteria, self::SEARCHABLE_COLUMNS, (int) Auth::id());
    }
}
