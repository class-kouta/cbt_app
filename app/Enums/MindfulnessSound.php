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

    /** Heroicons v2 outline 名（<x-icon name="..." />） */
    public function icon(): string
    {
        return match ($this) {
            self::FOREST => 'trees',
            self::STREAM => 'waves',
            self::JUNGLE => 'bolt',
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
            'icon' => $case->icon(),
        ], self::cases());
    }
}
