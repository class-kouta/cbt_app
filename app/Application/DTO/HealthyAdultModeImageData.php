<?php

namespace App\Application\DTO;

readonly class HealthyAdultModeImageData
{
    public function __construct(
        public ?string $content
    ) {
    }
}
