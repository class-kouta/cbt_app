<?php

namespace App\Domain\Entity;

use DateTimeImmutable;
use App\Domain\ValueObject\SimpleNotepadContent;

class SimpleNotepad
{
    private ?int $id;
    private SimpleNotepadContent $content;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        SimpleNotepadContent $content,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(string $content): self
    {
        $now = new DateTimeImmutable('now');
        return new self(null, new SimpleNotepadContent($content), $now, $now);
    }

    public static function reconstitute(
        int $id,
        string $content,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self($id, new SimpleNotepadContent($content), $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content->value();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function withId(int $id): self
    {
        return new self($id, $this->content, $this->createdAt, $this->updatedAt);
    }

    public function update(string $content): self
    {
        return new self(
            $this->id,
            new SimpleNotepadContent($content),
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }
}
