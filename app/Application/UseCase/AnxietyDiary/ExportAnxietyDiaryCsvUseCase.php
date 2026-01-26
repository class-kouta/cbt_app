<?php

namespace App\Application\UseCase\AnxietyDiary;

use App\Application\DTO\SearchCriteriaData;
use App\Application\Service\CsvExportService;
use App\Domain\Repository\AnxietyDiaryRepositoryInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * 不安日記CSV出力ユースケース
 */
class ExportAnxietyDiaryCsvUseCase
{
    /**
     * キーワード検索対象カラム
     */
    private const SEARCHABLE_COLUMNS = [
        'situation',
        'anxiety_thought',
        'actual_outcome',
    ];

    /**
     * CSVヘッダー
     */
    private const CSV_HEADERS = [
        'ID',
        '作成日時',
        '状況',
        '不安に思ったこと',
        '実際にどうなったか',
    ];

    public function __construct(
        private readonly AnxietyDiaryRepositoryInterface $repository,
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
                $item['situation'] ?? '',
                $item['anxiety_thought'] ?? '',
                $item['actual_outcome'] ?? '',
            ];
        }, $items);

        $filename = 'anxiety_diaries_' . $this->csvExportService->getDateSuffix() . '.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }
}
