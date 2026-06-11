<?php

namespace App\Application\DTO;

readonly class ExposureData
{
    public function __construct(
        public string $avoidanceTarget,
        public ?string $selfTalk = null,
        public ?string $overallReflection = null,
        public ?string $nextGoal = null
    ) {
    }
}
