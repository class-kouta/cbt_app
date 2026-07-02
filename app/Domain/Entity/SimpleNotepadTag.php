<?php

namespace App\Domain\Entity;

use App\Domain\ValueObject\SimpleNotepadTagName;
use App\Enums\SimpleNotepadTagColor;
use DateTimeImmutable;

class SimpleNotepadTag
{
    private ?int $id;
    private SimpleNotepadTagName $name;
    private SimpleNotepadTagColor $color;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        SimpleNotepadTagName $name,
        SimpleNotepadTagColor $color,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->color = $color;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(string $name, SimpleNotepadTagColor $color): self
    {
        $now = new DateTimeImmutable('now');

        return new self(null, new SimpleNotepadTagName($name), $color, $now, $now);
    }

    public static function reconstitute(
        int $id,
        string $name,
        string $color,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            new SimpleNotepadTagName($name),
            SimpleNotepadTagColor::fromString($color),
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name->value();
    }

    public function getColor(): SimpleNotepadTagColor
    {
        return $this->color;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function update(string $name, SimpleNotepadTagColor $color): self
    {
        return new self(
            $this->id,
            new SimpleNotepadTagName($name),
            $color,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }
}
