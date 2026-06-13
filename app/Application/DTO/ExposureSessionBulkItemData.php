<?php

namespace App\Application\DTO;

readonly class ExposureSessionBulkItemData
{
    public function __construct(
        public ?int $id = null,
        public ?int $hierarchyItemId = null,
        public ?int $sudsAfter = null,
        public ?string $reflection = null
    ) {
    }
}
