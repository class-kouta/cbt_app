<?php

namespace App\Application\DTO;

readonly class WritingDisclosureData
{
    public function __construct(
        public string $content
    ) {
    }
}
