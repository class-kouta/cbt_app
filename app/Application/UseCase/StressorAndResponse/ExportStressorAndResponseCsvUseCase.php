<?php

namespace App\Application\UseCase\StressorAndResponse;

use App\Application\DTO\SearchCriteriaData;
use App\Application\Service\CsvExportService;
use App\Domain\Repository\StressorAndResponseRepositoryInterface;
use Illuminate\Support\Facades\Auth;
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

    /**
     * スキーマ英語名から日本語名へのマッピング
     */
    private const SCHEMA_NAME_MAP = [
        // 第1領域：切断と拒絶
        'abandonment' => '見捨てられ/不安定スキーマ',
        'mistrust_abuse' => '不信/虐待スキーマ',
        'emotional_deprivation' => '情緒的剥奪スキーマ',
        'defectiveness_shame' => '欠陥/恥スキーマ',
        'social_isolation' => '社会的孤立/疎外スキーマ',
        // 第2領域：自律性と機能の障害
        'dependence_incompetence' => '依存/無能スキーマ',
        'vulnerability_to_harm' => '損害や疾病に対する脆弱性スキーマ',
        'enmeshment' => '巻き込まれ/未発達な自己スキーマ',
        'failure' => '失敗スキーマ',
        // 第3領域：制約の欠如
        'entitlement_grandiosity' => '権利要求/尊大さスキーマ',
        'insufficient_self_control' => '自制と自律の欠如スキーマ',
        // 第4領域：他者への志向
        'subjugation' => '服従スキーマ',
        'self_sacrifice' => '自己犠牲スキーマ',
        'approval_seeking' => '承認欲求/評価の追求スキーマ',
        // 第5領域：過剰警戒と抑制
        'negativity_pessimism' => '否定/悲観スキーマ',
        'emotional_inhibition' => '感情抑制スキーマ',
        'unrelenting_standards' => '厳密な基準/過度の批判スキーマ',
        'punitiveness' => '罰への懲罰的志向スキーマ',
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
        $items = $this->repository->searchAllForMember($criteria, self::SEARCHABLE_COLUMNS, (int) Auth::id());

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
                $this->translateSchemas($item['stimulated_schemas'] ?? []),
            ];
        }, $items);

        $filename = 'stressor_and_responses_' . $this->csvExportService->getDateSuffix() . '.csv';

        return $this->csvExportService->export(self::CSV_HEADERS, $rows, $filename);
    }

    /**
     * 英語スキーマ名を日本語に変換
     *
     * @param array<string>|null $schemas
     * @return string
     */
    private function translateSchemas(?array $schemas): string
    {
        if (empty($schemas)) {
            return '';
        }

        $translatedSchemas = array_map(function ($schema) {
            return self::SCHEMA_NAME_MAP[$schema] ?? $schema;
        }, $schemas);

        return implode(', ', $translatedSchemas);
    }
}
