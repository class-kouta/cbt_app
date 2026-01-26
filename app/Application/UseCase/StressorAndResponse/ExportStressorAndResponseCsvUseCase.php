<?php

namespace App\Application\UseCase\StressorAndResponse;

use App\Application\DTO\SearchCriteriaData;
use App\Application\Service\CsvExportService;
use App\Domain\Repository\StressorAndResponseRepositoryInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * ストレッサーとストレス反応CSV出力ユースケース
 */
class ExportStressorAndResponseCsvUseCase
{
    /**
     * キーワード検索対象カラム
     */
    private const SEARCHABLE_COLUMNS = [
        'stressor',
        'cognition',
        'mood',
        'body_reaction',
        'behavior',
    ];

    /**
     * CSVヘッダー
     */
    private const CSV_HEADERS = [
        'ID',
        '作成日時',
        'ストレッサー',
        '認知（自動思考）',
        '気分・感情',
        '身体反応',
        '行動',
        'タグ',
        '刺激されたスキーマ',
    ];

    public function __construct(
        private readonly StressorAndResponseRepositoryInterface $repository,
        private readonly CsvExportService $csvExportService
    ) {
    }

    /**
     * CSV出力を実行
     */
    public function handle(SearchCriteriaData $criteria): StreamedResponse
    {
        $items = $this->repository->searchAll($criteria, self::SEARCHABLE_COLUMNS);

        $rows = array_map(function ($item) {
            return [
                $item['id'],
                $this->csvExportService->formatDatetime($item['created_at']),
                $item['stressor'] ?? '',
                $item['cognition'] ?? '',
                $item['mood'] ?? '',
                $item['body_reaction'] ?? '',
                $item['behavior'] ?? '',
                $this->csvExportService->joinArray($item['tags'] ?? [], 'name'),
                $this->csvExportService->joinArray($item['stimulated_schemas'] ?? []),
            ];
        }, $items);

        $filename = 'stressor_and_responses_' . $this->csvExportService->getDateSuffix() . '.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }
}
