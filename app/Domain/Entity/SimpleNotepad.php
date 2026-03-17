<?php

namespace App\Domain\Entity;

use DateTimeImmutable;
use App\Domain\ValueObject\SimpleNotepadContent;
use App\Domain\ValueObject\SimpleNotepadTitle;

class SimpleNotepad
{
    private ?int $id;
    private SimpleNotepadTitle $title;
    private SimpleNotepadContent $content;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        SimpleNotepadTitle $title,
        SimpleNotepadContent $content,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(string $title, string $content): self
    {
        $now = new DateTimeImmutable('now');
        return new self(null, new SimpleNotepadTitle($title), new SimpleNotepadContent($content), $now, $now);
    }

    public static function reconstitute(
        int $id,
        string $title,
        string $content,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self($id, new SimpleNotepadTitle($title), new SimpleNotepadContent($content), $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title->value();
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
        return new self($id, $this->title, $this->content, $this->createdAt, $this->updatedAt);
    }

    public function update(string $title, string $content): self
    {
        return new self(
            $this->id,
            new SimpleNotepadTitle($title),
            new SimpleNotepadContent($content),
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }
}
