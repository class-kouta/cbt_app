<?php

namespace App\Application\UseCase\MyPage;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class GetTodayActivitiesUseCase
{
    /**
     * @return array{
     *     date: string,
     *     activities: list<array{label: string, count: int, message: string}>,
     *     has_activities: bool
     * }
     */
    public function handle(int $memberId, ?Carbon $date = null): array
    {
        $targetDate = $date ?? Carbon::today();
        $start = $targetDate->copy()->startOfDay();
        $end = $targetDate->copy()->addDay()->startOfDay();

        $counts = $this->fetchCountsViaUnionAll($memberId, $start, $end);

        $activities = [];

        foreach ($this->activityDefinitions() as $definition) {
            $count = $counts[$definition['key']] ?? 0;

            if ($count === 0) {
                continue;
            }

            $activities[] = [
                'label' => $definition['label'],
                'count' => $count,
                'message' => $this->buildMessage($definition['label'], $count),
            ];
        }

        return [
            'date' => $targetDate->toDateString(),
            'activities' => $activities,
            'has_activities' => count($activities) > 0,
        ];
    }

    /**
     * @return array<string, int>
     */
    private function fetchCountsViaUnionAll(int $memberId, Carbon $start, Carbon $end): array
    {
        $definitions = $this->activityDefinitions();
        $unionQuery = $this->buildCountSubquery($definitions[0], $memberId, $start, $end);

        foreach (array_slice($definitions, 1) as $definition) {
            $unionQuery = $unionQuery->unionAll(
                $this->buildCountSubquery($definition, $memberId, $start, $end),
            );
        }

        return DB::query()
            ->fromSub($unionQuery, 'activity_counts')
            ->pluck('count', 'activity_key')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    /**
     * @param array{
     *     key: string,
     *     label: string,
     *     table: string,
     *     parent_table?: string,
     *     parent_alias?: string,
     *     child_alias?: string,
     *     child_foreign_key?: string
     * } $definition
     */
    private function buildCountSubquery(
        array $definition,
        int $memberId,
        Carbon $start,
        Carbon $end,
    ): Builder {
        if (isset($definition['parent_table'])) {
            $childAlias = $definition['child_alias'];
            $parentAlias = $definition['parent_alias'];

            return DB::table("{$definition['table']} as {$childAlias}")
                ->join(
                    "{$definition['parent_table']} as {$parentAlias}",
                    "{$parentAlias}.id",
                    '=',
                    "{$childAlias}.{$definition['child_foreign_key']}",
                )
                ->selectRaw("'{$definition['key']}' as activity_key, COUNT(*) as count")
                ->where("{$parentAlias}.member_id", $memberId)
                ->where("{$childAlias}.created_at", '>=', $start)
                ->where("{$childAlias}.created_at", '<', $end);
        }

        return DB::table($definition['table'])
            ->selectRaw("'{$definition['key']}' as activity_key, COUNT(*) as count")
            ->where('member_id', $memberId)
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $end);
    }

    /**
     * @return list<array{
     *     key: string,
     *     label: string,
     *     table: string,
     *     parent_table?: string,
     *     parent_alias?: string,
     *     child_alias?: string,
     *     child_foreign_key?: string
     * }>
     */
    private function activityDefinitions(): array
    {
        return [
            ['key' => 'condition_check', 'label' => 'コンディションチェック', 'table' => 'condition_checks'],
            ['key' => 'self_compassion_journal', 'label' => 'セルフコンパッション日記', 'table' => 'self_compassion_journals'],
            ['key' => 'stress_person_encyclopedia', 'label' => 'ストレス人物図鑑', 'table' => 'stress_person_encyclopedias'],
            ['key' => 'stressor_and_response', 'label' => 'ストレッサーとストレス反応', 'table' => 'stressor_and_responses'],
            ['key' => 'column', 'label' => 'コラム法', 'table' => 'columns'],
            ['key' => 'problem_solving', 'label' => '問題解決法', 'table' => 'problem_solvings'],
            [
                'key' => 'problem_solving_plan',
                'label' => '実行計画',
                'table' => 'problem_solving_plans',
                'child_alias' => 'psp',
                'parent_table' => 'problem_solvings',
                'parent_alias' => 'ps',
                'child_foreign_key' => 'problem_solving_id',
            ],
            ['key' => 'exposure', 'label' => 'エクスポージャー療法', 'table' => 'exposures'],
            [
                'key' => 'exposure_session',
                'label' => 'エクスポージャー実施記録',
                'table' => 'exposure_sessions',
                'child_alias' => 'es',
                'parent_table' => 'exposures',
                'parent_alias' => 'e',
                'child_foreign_key' => 'exposure_id',
            ],
            ['key' => 'support_network', 'label' => 'サポートネットワーク', 'table' => 'support_networks'],
            ['key' => 'coping', 'label' => 'コーピング', 'table' => 'copings'],
            ['key' => 'chronology', 'label' => 'スキーマ年表', 'table' => 'chronologies'],
            ['key' => 'dialogue_work', 'label' => '対話ワーク', 'table' => 'dialogue_works'],
            ['key' => 'early_maladaptive_schema', 'label' => '早期不適応的スキーマ', 'table' => 'early_maladaptive_schemas'],
            ['key' => 'simple_notepad', 'label' => 'メモ帳', 'table' => 'simple_notepads'],
        ];
    }

    private function buildMessage(string $label, int $count): string
    {
        return sprintf('%sを%d件作成しました', $label, $count);
    }
}
