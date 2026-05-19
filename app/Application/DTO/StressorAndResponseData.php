<?php

namespace App\Application\DTO;

readonly class StressorAndResponseData
{
    /**
     * @param string $stressor
     * @param string|null $cognition
     * @param string|null $mood
     * @param string|null $bodyReaction
     * @param string|null $behavior
     */
    public function __construct(
        public string $stressor,
        public ?string $cognition,
        public ?string $mood,
        public ?string $bodyReaction,
        public ?string $behavior
    ) {
    }
}
