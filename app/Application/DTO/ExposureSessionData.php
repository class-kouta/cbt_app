<?php

namespace App\Application\DTO;

readonly class ExposureSessionData
{
    public function __construct(
        public ?int $hierarchyItemId = null,
        public ?int $sudsAfter = null,
        public ?string $reflection = null
    ) {
    }
}
