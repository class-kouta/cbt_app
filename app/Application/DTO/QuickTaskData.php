<?php

namespace App\Application\DTO;

readonly class QuickTaskData
{
    public function __construct(
        public string $content,
        public ?int $difficultyId = null,
        /** @var int[] */
        public array $tagIds = []
    ) {
    }
}
