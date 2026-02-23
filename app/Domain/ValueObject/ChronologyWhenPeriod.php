<?php

namespace App\Domain\ValueObject;

use DomainException;

readonly class ChronologyWhenPeriod
{
    private const MAX_LENGTH = 200;

    private string $value;

    public function __construct(string $raw)
    {
        $normalized = trim($raw);

        if ($normalized === '') {
            throw new DomainException('When period must not be empty.');
        }

        if (mb_strlen($normalized) > self::MAX_LENGTH) {
            throw new DomainException('When period must be at most ' . self::MAX_LENGTH . ' characters.');
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }
}
