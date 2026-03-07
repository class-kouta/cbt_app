<?php

namespace App\Application\DTO;

readonly class ChronologyData
{
    public function __construct(
        public string $whenPeriod,
        public ?string $environmentEvent,
        public ?string $experienceFeeling,
        public ?string $sentimentType = null
    ) {}
}
