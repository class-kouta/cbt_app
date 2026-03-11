<?php

namespace App\Enums;

enum ModeCategory: string
{
    case VULNERABLE_CHILD = '傷ついた子どもモード';
    case ANGRY_ADULT = '傷つける大人モード';
    case MALADAPTIVE_COPING = 'いたたけない対処モード';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
