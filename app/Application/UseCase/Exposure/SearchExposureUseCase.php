<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\SearchCriteriaData;
use App\Application\Service\ExposureResponseFormatter;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchExposureUseCase
{
    private const SEARCHABLE_COLUMNS = [
        'avoidance_target',
    ];

    public function __construct(
        private readonly ExposureRepositoryInterface $repository,
        private readonly ExposureResponseFormatter $formatter
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(SearchCriteriaData $criteria): array
    {
        $result = $this->repository->searchForMember($criteria, self::SEARCHABLE_COLUMNS, (int) Auth::id());

        return [
            'data' => array_map(
                fn ($exposure) => $this->formatter->exposureFromEntity($exposure),
                $result['data']
            ),
            'total' => $result['total'],
            'per_page' => $result['per_page'],
            'current_page' => $result['current_page'],
            'last_page' => $result['last_page'],
            'from' => $result['from'],
            'to' => $result['to'],
        ];
    }
}
