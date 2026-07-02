<?php

namespace App\Enums;

enum SimpleNotepadTagColor: string
{
    case Rose = 'rose';
    case Amber = 'amber';
    case Emerald = 'emerald';
    case Sky = 'sky';
    case Violet = 'violet';
    case Pink = 'pink';
    case Teal = 'teal';
    case Orange = 'orange';
    case Indigo = 'indigo';
    case Lime = 'lime';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::Emerald;
    }

    public static function defaultForIndex(int $index): self
    {
        $cases = self::cases();

        return $cases[abs($index) % count($cases)];
    }
}
