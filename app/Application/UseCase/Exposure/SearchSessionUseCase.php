<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\SessionSearchCriteriaData;
use App\Application\Service\ExposureResponseFormatter;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchSessionUseCase
{
    private const SEARCHABLE_COLUMNS = [
        'reflection',
    ];

    public function __construct(
        private readonly ExposureRepositoryInterface $repository,
        private readonly ExposureResponseFormatter $formatter
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(SessionSearchCriteriaData $criteria): array
    {
        $result = $this->repository->searchSessionsForMember($criteria, self::SEARCHABLE_COLUMNS, (int) Auth::id());

        return [
            'data' => array_map(
                fn (array $row) => $this->formatter->sessionSearchRowFromEntity(
                    $row['session'],
                    $row['avoidance_target'],
                    $row['hierarchy_item_content']
                ),
                $result['data']
            ),
            'total' => $result['total'],
            'current_page' => $result['current_page'],
            'last_page' => $result['last_page'],
            'per_page' => $result['per_page'],
            'from' => $result['from'],
            'to' => $result['to'],
        ];
    }
}
