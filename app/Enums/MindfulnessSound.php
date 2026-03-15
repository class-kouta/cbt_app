<?php

namespace App\Enums;

enum MindfulnessSound: string
{
    case FOREST = 'forest';
    case STREAM = 'stream';
    case JUNGLE = 'jungle';

    public function label(): string
    {
        return match ($this) {
            self::FOREST => '森と木陰と風、鳥の鳴き声',
            self::STREAM => '夕暮れの小川、鈴虫と風',
            self::JUNGLE => '雷雨のジャングルと動物達',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::FOREST => '🌲',
            self::STREAM => '🌅',
            self::JUNGLE => '🌴',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toFrontendArray(): array
    {
        return array_map(fn (self $case) => [
            'id' => $case->value,
            'label' => $case->label(),
            'emoji' => $case->emoji(),
        ], self::cases());
    }
}
