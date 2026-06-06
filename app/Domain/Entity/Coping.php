<?php

namespace App\Domain\Entity;

use DateTimeImmutable;
use App\Domain\ValueObject\CopingContent;

class Coping
{
    private ?int $id;
    private CopingContent $content;
    private int $point;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        CopingContent $content,
        int $point,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->content = $content;
        $this->point = $point;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(string $content): self
    {
        $now = new DateTimeImmutable('now');
        return new self(null, new CopingContent($content), 0, $now, $now);
    }

    public static function reconstitute(
        int $id,
        string $content,
        int $point,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self($id, new CopingContent($content), $point, $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content->value();
    }

    public function getPoint(): int
    {
        return $this->point;
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
        return new self($id, $this->content, $this->point, $this->createdAt, $this->updatedAt);
    }

    public function incrementPoint(): self
    {
        return new self(
            $this->id,
            $this->content,
            $this->point + 1,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }

    public function decrementPoint(): self
    {
        $newPoint = max(0, $this->point - 1);
        return new self(
            $this->id,
            $this->content,
            $newPoint,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }

    public function updateContent(string $content, ?int $point = null): self
    {
        return new self(
            $this->id,
            new CopingContent($content),
            $point ?? $this->point,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }
}
