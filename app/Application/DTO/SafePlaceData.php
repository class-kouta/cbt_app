<?php

namespace App\Application\DTO;

readonly class SafePlaceData
{
    public function __construct(
        public ?string $safeImage,
        public ?string $safeSomething
    ) {
    }
}
