<?php

namespace App\Application\DTO;

readonly class AnxietyDiaryData
{
    public function __construct(
        public string $situation,
        public ?string $anxietyThought,
        public ?string $actualOutcome,
        public ?int $stressorAndResponseId = null
    ) {
    }
}
