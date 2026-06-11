<?php

namespace App\Application\DTO;

readonly class ExposureHierarchyItemData
{
    public function __construct(
        public string $content,
        public int $sortOrder,
        public ?int $expectedSuds = null,
        public ?int $id = null
    ) {
    }
}
