<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class SafePlace
{
    private ?int $id;
    private ?string $safeImage;
    private ?string $safeSomething;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        ?string $safeImage,
        ?string $safeSomething,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->safeImage = $safeImage;
        $this->safeSomething = $safeSomething;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        ?string $safeImage,
        ?string $safeSomething
    ): self {
        $now = new DateTimeImmutable('now');
        return new self(null, $safeImage, $safeSomething, $now, $now);
    }

    public static function reconstitute(
        int $id,
        ?string $safeImage,
        ?string $safeSomething,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self($id, $safeImage, $safeSomething, $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSafeImage(): ?string
    {
        return $this->safeImage;
    }

    public function getSafeSomething(): ?string
    {
        return $this->safeSomething;
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
            $this->safeImage,
            $this->safeSomething,
            $this->createdAt,
            $this->updatedAt
        );
    }
}
