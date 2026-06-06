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
}
