<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\SearchCriteriaData;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchExposureUseCase
{
    private const SEARCHABLE_COLUMNS = [
        'avoidance_target',
        'self_talk',
        'overall_reflection',
        'next_goal',
    ];

    public function __construct(private readonly ExposureRepositoryInterface $repository)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(SearchCriteriaData $criteria): array
    {
        return $this->repository->searchForMember($criteria, self::SEARCHABLE_COLUMNS, (int) Auth::id());
    }
}
