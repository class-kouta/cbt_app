<?php

namespace App\Application\UseCase\ProblemSolving;

use App\Application\DTO\SearchCriteriaData;
use App\Application\Service\CsvExportService;
use App\Domain\Repository\ProblemSolvingRepositoryInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;

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
        '解決策（全て）※[番号] 内容 (効果%)[実行可能%]',
        '実行計画（全て）',
        '振り返り（全て）※改善レベル付き',
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
        $items = $this->repository->searchAllForMember($criteria, self::SEARCHABLE_COLUMNS, Auth::id());

        $rows = array_map(function ($item) {
            // 解決策をsort_order順にソートして1列にまとめる
            $solutions = $item['solutions'] ?? [];
            usort($solutions, fn ($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));

            $solutionTexts = [];
            foreach ($solutions as $index => $solution) {
                $effectiveness = $solution['effectiveness'] !== null ? "({$solution['effectiveness']}%)" : '';
                $feasibility = $solution['feasibility'] !== null ? "[{$solution['feasibility']}%]" : '';
                $details = ($effectiveness || $feasibility) ? " {$effectiveness}{$feasibility}" : '';
                $solutionTexts[] = '[' . ($index + 1) . '] ' . $solution['content'] . $details;
            }
            $allSolutions = implode(' / ', $solutionTexts);

            // 全てのplanをplan_number順にソートして、実行計画と振り返りを結合
            $plans = $item['plans'] ?? [];
            usort($plans, fn ($a, $b) => ($a['plan_number'] ?? 0) <=> ($b['plan_number'] ?? 0));

            // 記入されている実行計画を全て結合
            $actionPlans = array_filter(
                array_map(fn ($p) => $p['action_plan'] ?? '', $plans),
                fn ($v) => !empty(trim($v))
            );
            $allActionPlans = !empty($actionPlans)
                ? implode(' / ', array_map(fn ($v, $k) => '[' . ($k + 1) . '] ' . $v, array_values($actionPlans), array_keys($actionPlans)))
                : '';

            // 記入されている振り返りを全て結合（改善レベル付き）
            $reflectionTexts = [];
            foreach ($plans as $index => $plan) {
                $reflection = $plan['reflection'] ?? '';
                if (!empty(trim($reflection))) {
                    $text = '[' . ($index + 1) . '] ' . $reflection;
                    $level = $plan['improvement_level'] ?? null;
                    if ($level !== null) {
                        $text .= ' (改善Lv.' . $level . ')';
                    }
                    $reflectionTexts[] = $text;
                }
            }
            $allReflections = implode(' / ', $reflectionTexts);

            return [
                $item['id'],
                $this->csvExportService->formatDatetime($item['created_at']),
                $item['problem_situation'] ?? '',
                $item['improved_image'] ?? '',
                $allSolutions,
                $allActionPlans,
                $allReflections,
                $this->csvExportService->joinArray($item['tags'] ?? [], 'name'),
            ];
        }, $items);

        $filename = 'problem_solvings_' . $this->csvExportService->getDateSuffix() . '.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }
}
