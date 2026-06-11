<?php

namespace App\Application\DTO;

readonly class ExposureSessionData
{
    public function __construct(
        public ?int $hierarchyItemId = null,
        public ?string $actionPlan = null,
        public ?int $sudsBefore = null,
        public ?int $sudsPeak = null,
        public ?int $sudsAfter = null,
        public ?string $performedAt = null,
        public ?string $reflection = null
    ) {
    }
}
