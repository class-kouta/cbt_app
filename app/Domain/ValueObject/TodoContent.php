<?php

namespace App\Domain\ValueObject;

use DomainException;

readonly class TodoContent
{
    private const MAX_LENGTH = 100;

    private string $value;

    public function __construct(string $raw)
    {
        $normalized = trim($raw);

        if ($normalized === '') {
            throw new DomainException('Todo content must not be empty.');
        }

        if (mb_strlen($normalized) > self::MAX_LENGTH) {
            throw new DomainException('Todo content must be at most '.self::MAX_LENGTH.' characters.');
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }
}
