<?php

namespace App\Application\DTO;

readonly class ModeMapData
{
    public function __construct(
        public ?string $woundedChildMode,
        public ?string $hurtfulAdultMode,
        public ?string $unacceptableCopingMode,
        public ?string $healthyHappyChildMode,
        public ?string $healthyAdultMode
    ) {}
}
