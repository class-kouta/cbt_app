<?php

namespace App\Domain\ValueObject;

use DomainException;

readonly class SimpleNotepadTitle
{
    private const MAX_LENGTH = 255;

    private string $value;

    public function __construct(string $raw)
    {
        $normalized = trim($raw);

        if (mb_strlen($normalized) > self::MAX_LENGTH) {
            throw new DomainException('Simple notepad title must be at most '.self::MAX_LENGTH.' characters.');
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }
}
