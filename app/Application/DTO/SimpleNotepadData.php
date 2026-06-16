<?php

namespace App\Application\DTO;

readonly class SimpleNotepadData
{
    /**
     * @param array<int> $tagIds
     */
    public function __construct(
        public string $content,
        public array $tagIds = [],
    ) {
    }
}
