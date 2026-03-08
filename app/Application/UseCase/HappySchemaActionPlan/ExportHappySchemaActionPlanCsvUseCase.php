<?php

namespace App\Application\UseCase\HappySchemaActionPlan;

use App\Application\Service\CsvExportService;
use App\Infrastructure\Database\Models\HappySchemaActionPlan;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportHappySchemaActionPlanCsvUseCase
{
    private const CSV_HEADERS = [
        'ID',
        '作成日時',
        'ハッピースキーマ',
        'ハッピースキーマに基づく行動計画',
    ];

    public function __construct(
        private readonly CsvExportService $csvExportService
    ) {
    }

    public function handle(): StreamedResponse
    {
        $items = HappySchemaActionPlan::orderByDesc('created_at')
            ->get()
            ->map(fn ($item) => $item->toArray())
            ->toArray();

        $rows = array_map(function ($item) {
            $sanitize = function ($value) {
                if (is_string($value) && strlen($value) > 0 && in_array($value[0], ['=', '+', '-', '@'])) {
                    return "'" . $value;
                }
                return $value;
            };
            return [
                $item['id'],
                $this->csvExportService->formatDatetime($item['created_at']),
                $sanitize($item['happy_schema'] ?? ''),
                $sanitize($item['action_plan'] ?? ''),
            ];
        }, $items);

        $filename = 'happy_schema_action_plans_' . $this->csvExportService->getDateSuffix() . '.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }
}
