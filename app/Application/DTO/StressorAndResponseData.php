<?php

namespace App\Application\DTO;

readonly class StressorAndResponseData
{
    public function __construct(
        public string $stressor,
        public ?string $cognition,
        public ?string $mood,
        public ?string $bodyReaction,
        public ?string $behavior
    ) {
    }
}
