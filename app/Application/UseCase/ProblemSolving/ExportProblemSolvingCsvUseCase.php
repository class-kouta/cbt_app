<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Application\DTO\SearchCriteriaData;
use App\Application\Service\CsvExportService;
use App\Domain\Repository\ProblemSolvingRepositoryInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * 問題解決法CSV出力ユースケース
 */
class ExportProblemSolvingCsvUseCase
{
    /**
     * キーワード検索対象カラム
     */
    private const SEARCHABLE_COLUMNS = [
        'problem_situation',
        'improved_image',
    ];

    /**
     * CSVヘッダー
     */
    private const CSV_HEADERS = [
        'ID',
        '作成日時',
        '問題状況',
        '改善イメージ',
        '解決策1',
        '解決策2',
        '解決策3',
        '解決策4',
        '解決策5',
        '解決策6',
        '解決策7',
        '実行計画',
        '振り返り',
        'タグ',
    ];

    public function __construct(
        private readonly ProblemSolvingRepositoryInterface $repository,
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
            // 解決策をsort_order順にソートして7つ分のカラムに配置
            $solutions = $item['solutions'] ?? [];
            usort($solutions, fn ($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));

            $solutionColumns = [];
            for ($i = 0; $i < 7; $i++) {
                $solution = $solutions[$i] ?? null;
                if ($solution) {
                    $effectiveness = $solution['effectiveness'] !== null ? "({$solution['effectiveness']}%)" : '';
                    $feasibility = $solution['feasibility'] !== null ? "[{$solution['feasibility']}%]" : '';
                    $solutionColumns[] = $solution['content'] . ($effectiveness || $feasibility ? " {$effectiveness}{$feasibility}" : '');
                } else {
                    $solutionColumns[] = '';
                }
            }

            // 最新のplanを取得（複数ある場合は最後のもの）
            $plans = $item['plans'] ?? [];
            $latestPlan = !empty($plans) ? end($plans) : null;

            return [
                $item['id'],
                $this->csvExportService->formatDatetime($item['created_at']),
                $item['problem_situation'] ?? '',
                $item['improved_image'] ?? '',
                ...$solutionColumns,
                $latestPlan['action_plan'] ?? '',
                $latestPlan['reflection'] ?? '',
                $this->csvExportService->joinArray($item['tags'] ?? [], 'name'),
            ];
        }, $items);

        $filename = 'problem_solvings_' . $this->csvExportService->getDateSuffix() . '.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }
}
