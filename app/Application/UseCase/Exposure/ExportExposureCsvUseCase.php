<?php

namespace App\Application\UseCase\Exposure;

use App\Application\DTO\SearchCriteriaData;
use App\Application\Service\CsvExportService;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportExposureCsvUseCase
{
    private const SEARCHABLE_COLUMNS = [
        'avoidance_target',
    ];

    private const CSV_HEADERS = [
        'ID',
        '作成日時',
        '回避していること',
        '不安階層表',
        '実施記録',
    ];

    public function __construct(
        private readonly ExposureRepositoryInterface $repository,
        private readonly CsvExportService $csvExportService
    ) {
    }

    public function handle(SearchCriteriaData $criteria): StreamedResponse
    {
        $memberId = (int) Auth::id();

        $rows = (function () use ($criteria, $memberId) {
            foreach ($this->repository->cursorAllForMember($criteria, self::SEARCHABLE_COLUMNS, $memberId) as $item) {
                yield $this->formatRow($item);
            }
        })();

        $filename = 'exposures_'.$this->csvExportService->getDateSuffix().'.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<int, mixed>
     */
    private function formatRow(array $item): array
    {
        $hierarchyItems = $item['hierarchy_items'] ?? [];

        $hierarchyTexts = [];
        foreach ($hierarchyItems as $index => $hierarchyItem) {
            $anxietyLevel = $hierarchyItem['expected_suds'] !== null ? " (不安レベル: {$hierarchyItem['expected_suds']})" : '';
            $hierarchyTexts[] = '['.($index + 1).'] '.$hierarchyItem['content'].$anxietyLevel;
        }

        $sessions = $item['sessions'] ?? [];

        $sessionTexts = [];
        foreach ($sessions as $index => $session) {
            $parts = [];
            if ($session['suds_after'] !== null) {
                $parts[] = '不安レベル(後'.$session['suds_after'].')';
            }
            if (! empty(trim($session['reflection'] ?? ''))) {
                $parts[] = '振り返り: '.$session['reflection'];
            }
            if (! empty($parts)) {
                $sessionTexts[] = '['.($index + 1).'] '.implode(' / ', $parts);
            }
        }

        return [
            $item['id'],
            $this->csvExportService->formatDatetime($item['created_at']),
            $item['avoidance_target'] ?? '',
            implode(' / ', $hierarchyTexts),
            implode(' / ', $sessionTexts),
        ];
    }
}
