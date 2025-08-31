<?php

namespace App\Application\DTO;

readonly class TodoData
{
    public function __construct(
        public int $difficultyId,
        public string $content,
        /** @var int[] */
        public array $tagIds = []
    ) {
    }
}
