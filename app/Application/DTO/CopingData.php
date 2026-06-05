<?php

namespace App\Application\DTO;

readonly class CopingData
{
    public function __construct(
        public string $content,
        public ?int $point = null
    ) {
    }
}
