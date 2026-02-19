<?php

namespace App\Application\DTO;

readonly class ProblemSolvingPlanData
{
    public function __construct(
        public ?string $actionPlan = null,
        public ?string $reflection = null,
        public ?int $improvementLevel = null
    ) {
    }
}
