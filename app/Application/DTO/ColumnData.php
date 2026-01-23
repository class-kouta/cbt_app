<?php

namespace App\Application\DTO;

readonly class ColumnData
{
    /**
     * @param array<int> $tagIds
     */
    public function __construct(
        public string $situation,
        public ?string $mood,
        public ?string $automaticThought,
        public ?string $evidence,
        public ?string $counterEvidence,
        public ?string $adaptiveThought,
        public ?string $currentMood,
        public ?string $notes = null,
        public ?int $stressorAndResponseId = null,
        public array $tagIds = []
    ) {
    }
}
