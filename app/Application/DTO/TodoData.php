<?php

namespace App\Application\DTO;

class TodoData
{
    public function __construct(
        public readonly int $difficultyId,
        public readonly string $content,
        /** @var int[] */
        public readonly array $tagIds = []
    ) {
    }
}

