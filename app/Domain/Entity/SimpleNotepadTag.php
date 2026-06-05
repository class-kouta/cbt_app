<?php

namespace App\Domain\Entity;

use App\Domain\ValueObject\SimpleNotepadTagName;
use DateTimeImmutable;

class SimpleNotepadTag
{
    private ?int $id;
    private SimpleNotepadTagName $name;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        SimpleNotepadTagName $name,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(string $name): self
    {
        $now = new DateTimeImmutable('now');

        return new self(null, new SimpleNotepadTagName($name), $now, $now);
    }

    public static function reconstitute(
        int $id,
        string $name,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self($id, new SimpleNotepadTagName($name), $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name->value();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateName(string $name): self
    {
        return new self(
            $this->id,
            new SimpleNotepadTagName($name),
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }
}
