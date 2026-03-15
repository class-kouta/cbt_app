<?php

namespace App\Enums;

enum MindfulnessDuration: int
{
    case FIVE = 5;
    case TEN = 10;
    case FIFTEEN = 15;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
