<?php

namespace App\Application\DTO;

readonly class ProblemSolvingSolutionData
{
    public function __construct(
        public string $content,
        public int $sortOrder,
        public ?int $effectiveness = null,
        public ?int $feasibility = null
    ) {
    }
}
