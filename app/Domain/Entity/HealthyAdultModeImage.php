<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class HealthyAdultModeImage
{
    private ?int $id;
    private ?string $content;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        ?string $content,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(?string $content): self
    {
        $now = new DateTimeImmutable('now');
        return new self(null, $content, $now, $now);
    }

    public static function reconstitute(
        int $id,
        ?string $content,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self($id, $content, $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
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
        return new self(
            $id,
            $this->content,
            $this->createdAt,
            $this->updatedAt
        );
    }
}
