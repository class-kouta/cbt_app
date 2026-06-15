<?php

namespace App\Application\DTO;

readonly class ExposureData
{
    public function __construct(
        public string $avoidanceTarget
    ) {
    }
}
