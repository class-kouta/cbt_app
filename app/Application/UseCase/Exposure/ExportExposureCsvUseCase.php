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
        'self_talk',
        'overall_reflection',
        'next_goal',
    ];

    private const CSV_HEADERS = [
        'ID',
        '作成日時',
        '回避していること',
        '自分への声かけ',
        '不安階層表',
        '実施記録',
        '全体振り返り',
        '次の目標',
    ];

    public function __construct(
        private readonly ExposureRepositoryInterface $repository,
        private readonly CsvExportService $csvExportService
    ) {
    }

    public function handle(SearchCriteriaData $criteria): StreamedResponse
    {
        $items = $this->repository->searchAllForMember($criteria, self::SEARCHABLE_COLUMNS, (int) Auth::id());

        $rows = array_map(function ($item) {
            $hierarchyItems = $item['hierarchy_items'] ?? [];
            usort($hierarchyItems, fn ($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));

            $hierarchyTexts = [];
            foreach ($hierarchyItems as $index => $hierarchyItem) {
                $anxietyLevel = $hierarchyItem['expected_suds'] !== null ? " (不安レベル: {$hierarchyItem['expected_suds']})" : '';
                $hierarchyTexts[] = '[' . ($index + 1) . '] ' . $hierarchyItem['content'] . $anxietyLevel;
            }

            $sessions = $item['sessions'] ?? [];
            usort($sessions, fn ($a, $b) => ($a['session_number'] ?? 0) <=> ($b['session_number'] ?? 0));

            $sessionTexts = [];
            foreach ($sessions as $index => $session) {
                $parts = [];
                if (! empty(trim($session['action_plan'] ?? ''))) {
                    $parts[] = '計画: ' . $session['action_plan'];
                }
                $anxietyLevelParts = [];
                if ($session['suds_before'] !== null) {
                    $anxietyLevelParts[] = '前' . $session['suds_before'];
                }
                if ($session['suds_peak'] !== null) {
                    $anxietyLevelParts[] = '最高' . $session['suds_peak'];
                }
                if ($session['suds_after'] !== null) {
                    $anxietyLevelParts[] = '後' . $session['suds_after'];
                }
                if (! empty($anxietyLevelParts)) {
                    $parts[] = '不安レベル(' . implode('→', $anxietyLevelParts) . ')';
                }
                if (! empty(trim($session['reflection'] ?? ''))) {
                    $parts[] = '振り返り: ' . $session['reflection'];
                }
                if (! empty($parts)) {
                    $sessionTexts[] = '[' . ($index + 1) . '] ' . implode(' / ', $parts);
                }
            }

            return [
                $item['id'],
                $this->csvExportService->formatDatetime($item['created_at']),
                $item['avoidance_target'] ?? '',
                $item['self_talk'] ?? '',
                implode(' / ', $hierarchyTexts),
                implode(' / ', $sessionTexts),
                $item['overall_reflection'] ?? '',
                $item['next_goal'] ?? '',
            ];
        }, $items);

        $filename = 'exposures_' . $this->csvExportService->getDateSuffix() . '.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }
}
