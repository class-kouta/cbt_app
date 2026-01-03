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
     * @param array<string>|null $stimulatedSchemas
     */
    public function __construct(
        public string $stressor,
        public ?string $cognition,
        public ?string $mood,
        public ?string $bodyReaction,
        public ?string $behavior,
        public ?array $stimulatedSchemas = null
    ) {
    }
}
