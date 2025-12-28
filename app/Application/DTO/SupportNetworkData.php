<?php

namespace App\Application\DTO;

readonly class SupportNetworkData
{
    public function __construct(
        public string $name,
        public ?int $point = null
    ) {
    }
}
