<?php

namespace App\Application\DTO;

readonly class SimpleNotepadData
{
    public function __construct(
        public string $content
    ) {
    }
}
