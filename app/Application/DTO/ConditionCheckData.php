<?php

namespace App\Application\DTO;

class ConditionCheckData
{
    public function __construct(
        public readonly int $mood,
        public readonly int $fatigue,
        public readonly int $anxiety,
        public readonly int $sleepiness,
        public readonly int $physicalCondition,
        public readonly ?string $memo,
    ) {
    }
}
