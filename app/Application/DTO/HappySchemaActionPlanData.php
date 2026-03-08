<?php

namespace App\Application\DTO;

readonly class HappySchemaActionPlanData
{
    public function __construct(
        public ?string $happySchema,
        public ?string $actionPlan
    ) {}
}
