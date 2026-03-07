<?php

namespace App\Application\UseCase\Chronology;

use App\Application\Service\CsvExportService;
use App\Infrastructure\Database\Models\Chronology;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportChronologyCsvUseCase
{
    private const CSV_HEADERS = [
        'ID',
        '作成日時',
        'いつ',
        '環境・出来事',
        '体験・感じたこと・思ったこと',
        'タグ',
    ];

    public function __construct(
        private readonly CsvExportService $csvExportService
    ) {}

    public function handle(): StreamedResponse
    {
        $items = Chronology::orderByDesc('created_at')
            ->get()
            ->map(fn ($item) => $item->toArray())
            ->toArray();

        $sentimentLabels = [
            'positive' => 'ポジティブ',
            'negative' => 'ネガティブ',
        ];

        $rows = array_map(function ($item) use ($sentimentLabels) {
            return [
                $item['id'],
                $this->csvExportService->formatDatetime($item['created_at']),
                $item['when_period'] ?? '',
                $item['environment_event'] ?? '',
                $item['experience_feeling'] ?? '',
                $sentimentLabels[$item['sentiment_type'] ?? ''] ?? '',
            ];
        }, $items);

        $filename = 'chronologies_'.$this->csvExportService->getDateSuffix().'.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }
}
