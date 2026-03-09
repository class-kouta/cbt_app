<?php

namespace App\Domain\ValueObject;

use DomainException;

readonly class DialogueWorkContent
{
    private const MAX_LENGTH = 50000;

    private string $value;

    public function __construct(string $raw)
    {
        $normalized = trim($raw);

        if ($normalized === '') {
            throw new DomainException('Dialogue work content must not be empty.');
        }

        if (mb_strlen($normalized) > self::MAX_LENGTH) {
            throw new DomainException('Dialogue work content must be at most '.self::MAX_LENGTH.' characters.');
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }
}
