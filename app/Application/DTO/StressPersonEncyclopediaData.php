<?php

namespace App\Application\DTO;

class StressPersonEncyclopediaData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $relationship,
        public readonly ?string $difficultTraits,
        public readonly ?string $myReaction,
        public readonly ?string $copingStrategy,
        public readonly ?string $notes,
    ) {
    }
}
