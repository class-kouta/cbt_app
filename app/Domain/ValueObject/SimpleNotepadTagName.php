<?php

namespace App\Domain\ValueObject;

use DomainException;

readonly class SimpleNotepadTagName
{
    private const MAX_LENGTH = 10;

    private string $value;

    public function __construct(string $raw)
    {
        $normalized = trim($raw);

        if ($normalized === '') {
            throw new DomainException('メモ帳のタグ名は空にできません。');
        }

        if (mb_strlen($normalized) > self::MAX_LENGTH) {
            throw new DomainException('メモ帳のタグ名は最大'.self::MAX_LENGTH.'文字までです。');
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }
}
