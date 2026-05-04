<?php

namespace App\Application\UseCase\Column;

use App\Application\DTO\SearchCriteriaData;
use App\Application\Service\CsvExportService;
use App\Domain\Repository\ColumnRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * コラム法CSV出力ユースケース
 */
class ExportColumnCsvUseCase
{
    /**
     * キーワード検索対象カラム
     */
    private const SEARCHABLE_COLUMNS = [
        'situation',
        'mood',
        'automatic_thought',
        'evidence',
        'counter_evidence',
        'adaptive_thought',
        'current_mood',
        'notes',
    ];

    /**
     * CSVヘッダー
     */
    private const CSV_HEADERS = [
        'ID',
        '作成日時',
        '状況',
        '感情',
        '自動思考',
        '根拠',
        '反証',
        '適応的思考',
        '現在の感情',
        'メモ',
        'タグ',
    ];

    public function __construct(
        private readonly ColumnRepositoryInterface $repository,
        private readonly CsvExportService $csvExportService
    ) {
    }

    /**
     * CSV出力を実行
     */
    public function handle(SearchCriteriaData $criteria): StreamedResponse
    {
        $items = $this->repository->searchAllForMember($criteria, self::SEARCHABLE_COLUMNS, (int) Auth::id());

        $rows = array_map(function ($item) {
            return [
                $item['id'],
                $this->csvExportService->formatDatetime($item['created_at']),
                $item['situation'] ?? '',
                $item['mood'] ?? '',
                $item['automatic_thought'] ?? '',
                $item['evidence'] ?? '',
                $item['counter_evidence'] ?? '',
                $item['adaptive_thought'] ?? '',
                $item['current_mood'] ?? '',
                $item['notes'] ?? '',
                $this->csvExportService->joinArray($item['tags'] ?? [], 'name'),
            ];
        }, $items);

        $filename = 'columns_' . $this->csvExportService->getDateSuffix() . '.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }
}
