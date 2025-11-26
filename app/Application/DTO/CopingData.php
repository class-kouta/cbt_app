<?php

namespace App\Application\DTO;

readonly class CopingData
{
    public function __construct(
        public string $content,
        /** @var int[] */
        public array $copingTagIds = [],
        public ?int $point = null
    ) {
    }
}
