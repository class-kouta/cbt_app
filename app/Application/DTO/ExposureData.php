<?php

namespace App\Application\DTO;

readonly class ExposureData
{
    /**
     * @param array<int> $tagIds
     */
    public function __construct(
        public string $avoidanceTarget,
        public ?string $exposureType = null,
        public ?string $selfTalk = null,
        public ?string $overallReflection = null,
        public ?string $nextGoal = null,
        public array $tagIds = []
    ) {
    }
}
