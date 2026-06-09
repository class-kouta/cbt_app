<?php

namespace App\Enums;

enum ConditionCheckRating: int
{
    case Level1 = 1;
    case Level2 = 2;
    case Level3 = 3;
    case Level4 = 4;
    case Level5 = 5;

    /**
     * @return array<string, list<string>>
     */
    public static function labelsByField(): array
    {
        return [
            'mood' => ['良い', 'まあまあ良い', '普通', 'まあまあ悪い', '悪い'],
            'fatigue' => ['疲れていない', 'あまり疲れていない', '普通', 'まあまあ疲れている', '疲れている'],
            'anxiety' => ['ない', 'あまりない', '普通', 'まあまあある', 'ある'],
            'sleepiness' => ['スッキリ', 'まあまあスッキリ', '普通', 'まあまあ眠い', '眠い'],
            'physical_condition' => ['良い', 'まあまあ良い', '普通', 'まあまあ悪い', '悪い'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function fieldLabels(): array
    {
        return [
            'mood' => '気分',
            'fatigue' => '疲労感',
            'anxiety' => '不安',
            'sleepiness' => '眠気',
            'physical_condition' => '体の調子',
        ];
    }

    public static function labelFor(string $field, int $value): string
    {
        $labels = self::labelsByField()[$field] ?? [];

        return $labels[$value - 1] ?? '';
    }

    /**
     * @return list<int>
     */
    public static function values(): array
    {
        return [1, 2, 3, 4, 5];
    }

    public static function maxScore(): int
    {
        return count(self::fieldLabels()) * self::Level5->value;
    }

    public static function calculateTotalScore(
        int $mood,
        int $fatigue,
        int $anxiety,
        int $sleepiness,
        int $physicalCondition,
    ): int {
        return $mood + $fatigue + $anxiety + $sleepiness + $physicalCondition;
    }

    /**
     * 合計スコアに応じた抽象ステータス（低いほど良好）
     */
    public static function scoreStatusFor(int $score): string
    {
        return match (true) {
            $score <= 9 => 'excellent',
            $score <= 14 => 'good',
            $score <= 19 => 'warning',
            default => 'danger',
        };
    }

    /**
     * 評価値に応じたバッジのCSSクラス（1=良い→青、5=悪い→赤）
     */
    public static function badgeClassFor(int $value): string
    {
        return match ($value) {
            self::Level1->value => 'bg-blue-100 text-blue-800',
            self::Level2->value => 'bg-sky-100 text-sky-800',
            self::Level3->value => 'bg-green-100 text-green-800',
            self::Level4->value => 'bg-orange-100 text-orange-800',
            self::Level5->value => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function badgeClassesByValue(): array
    {
        $classes = [];

        foreach (self::values() as $value) {
            $classes[$value] = self::badgeClassFor($value);
        }

        return $classes;
    }
}
