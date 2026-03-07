<?php

namespace App\Application\UseCase\ModeMap;

use App\Application\Service\CsvExportService;
use App\Infrastructure\Database\Models\ModeMap;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportModeMapCsvUseCase
{
    private const CSV_HEADERS = [
        'ID',
        '作成日時',
        '傷ついた子どもモード',
        '傷つける大人モード',
        'いただけない対処モード',
        'ヘルシーモード(幸せな子どもモード)',
        'ヘルシーモード(ヘルシーな大人モード)',
    ];

    public function __construct(
        private readonly CsvExportService $csvExportService
    ) {
    }

    public function handle(): StreamedResponse
    {
        $items = ModeMap::orderByDesc('created_at')
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
                $sanitize($item['wounded_child_mode'] ?? ''),
                $sanitize($item['hurtful_adult_mode'] ?? ''),
                $sanitize($item['unacceptable_coping_mode'] ?? ''),
                $sanitize($item['healthy_happy_child_mode'] ?? ''),
                $sanitize($item['healthy_adult_mode'] ?? ''),
            ];
        }, $items);

        $filename = 'mode_maps_' . $this->csvExportService->getDateSuffix() . '.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }
}
