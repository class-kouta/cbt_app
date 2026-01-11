<?php

namespace App\Application\DTO;

readonly class ProblemSolvingData
{
    public function __construct(
        public string $problemSituation,
        public ?string $improvedImage = null
    ) {
    }
}
