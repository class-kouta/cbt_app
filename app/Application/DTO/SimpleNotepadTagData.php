<?php

namespace App\Application\DTO;

readonly class SimpleNotepadTagData
{
    public function __construct(
        public string $name,
        public ?string $color = null,
    ) {
    }
}
