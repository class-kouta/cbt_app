<?php

namespace App\Application\UseCase\MyPage;

use App\Infrastructure\Database\Models\Chronology;
use App\Infrastructure\Database\Models\Column;
use App\Infrastructure\Database\Models\ConditionCheck;
use App\Infrastructure\Database\Models\Coping;
use App\Infrastructure\Database\Models\DialogueWork;
use App\Infrastructure\Database\Models\EarlyMaladaptiveSchema;
use App\Infrastructure\Database\Models\Exposure;
use App\Infrastructure\Database\Models\ExposureSession;
use App\Infrastructure\Database\Models\ProblemSolving;
use App\Infrastructure\Database\Models\ProblemSolvingPlan;
use App\Infrastructure\Database\Models\SelfCompassionJournal;
use App\Infrastructure\Database\Models\SimpleNotepad;
use App\Infrastructure\Database\Models\StressPersonEncyclopedia;
use App\Infrastructure\Database\Models\StressorAndResponse;
use App\Infrastructure\Database\Models\SupportNetwork;
use App\Infrastructure\Database\Models\WritingDisclosure;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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

        $activities = [];

        foreach ($this->activityDefinitions() as $definition) {
            $count = $this->countForMemberOnDate($definition, $memberId, $start, $end);

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
     * @return list<array{
     *     label: string,
     *     model: class-string<Model>,
     *     relation?: string
     * }>
     */
    private function activityDefinitions(): array
    {
        return [
            ['label' => 'コンディションチェック', 'model' => ConditionCheck::class],
            ['label' => '筆記開示', 'model' => WritingDisclosure::class],
            ['label' => 'セルフコンパッション日記', 'model' => SelfCompassionJournal::class],
            ['label' => 'ストレス人物図鑑', 'model' => StressPersonEncyclopedia::class],
            ['label' => 'ストレッサーとストレス反応', 'model' => StressorAndResponse::class],
            ['label' => 'コラム法', 'model' => Column::class],
            ['label' => '問題解決法', 'model' => ProblemSolving::class],
            ['label' => '実行計画', 'model' => ProblemSolvingPlan::class, 'relation' => 'problemSolving'],
            ['label' => 'エクスポージャー療法', 'model' => Exposure::class],
            ['label' => 'エクスポージャー実施記録', 'model' => ExposureSession::class, 'relation' => 'exposure'],
            ['label' => 'サポートネットワーク', 'model' => SupportNetwork::class],
            ['label' => 'コーピング', 'model' => Coping::class],
            ['label' => 'スキーマ年表', 'model' => Chronology::class],
            ['label' => '対話ワーク', 'model' => DialogueWork::class],
            ['label' => '早期不適応的スキーマ', 'model' => EarlyMaladaptiveSchema::class],
            ['label' => 'メモ帳', 'model' => SimpleNotepad::class],
        ];
    }

    /**
     * @param array{
     *     label: string,
     *     model: class-string<Model>,
     *     relation?: string
     * } $definition
     */
    private function countForMemberOnDate(
        array $definition,
        int $memberId,
        Carbon $start,
        Carbon $end,
    ): int {
        /** @var class-string<Model> $modelClass */
        $modelClass = $definition['model'];
        $query = $modelClass::query()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $end);

        if (isset($definition['relation'])) {
            $relation = $definition['relation'];

            return $query->whereHas($relation, function ($builder) use ($memberId) {
                $builder->where('member_id', $memberId);
            })->count();
        }

        return $query->where('member_id', $memberId)->count();
    }

    private function buildMessage(string $label, int $count): string
    {
        return sprintf('%sを%d件作成しました', $label, $count);
    }
}
