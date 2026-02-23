<?php

namespace App\Domain\Entity;

use DateTimeImmutable;
use App\Domain\ValueObject\ChronologyWhenPeriod;

class Chronology
{
    private ?int $id;
    private ChronologyWhenPeriod $whenPeriod;
    private ?string $environmentEvent;
    private ?string $experienceFeeling;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        ChronologyWhenPeriod $whenPeriod,
        ?string $environmentEvent,
        ?string $experienceFeeling,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->whenPeriod = $whenPeriod;
        $this->environmentEvent = $environmentEvent;
        $this->experienceFeeling = $experienceFeeling;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        string $whenPeriod,
        ?string $environmentEvent,
        ?string $experienceFeeling
    ): self {
        $now = new DateTimeImmutable('now');
        return new self(
            null,
            new ChronologyWhenPeriod($whenPeriod),
            $environmentEvent,
            $experienceFeeling,
            $now,
            $now
        );
    }

    public static function reconstitute(
        int $id,
        string $whenPeriod,
        ?string $environmentEvent,
        ?string $experienceFeeling,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            new ChronologyWhenPeriod($whenPeriod),
            $environmentEvent,
            $experienceFeeling,
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWhenPeriod(): string
    {
        return $this->whenPeriod->value();
    }

    public function getEnvironmentEvent(): ?string
    {
        return $this->environmentEvent;
    }

    public function getExperienceFeeling(): ?string
    {
        return $this->experienceFeeling;
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
            $this->whenPeriod,
            $this->environmentEvent,
            $this->experienceFeeling,
            $this->createdAt,
            $this->updatedAt
        );
    }

    public function update(
        string $whenPeriod,
        ?string $environmentEvent,
        ?string $experienceFeeling
    ): self {
        return new self(
            $this->id,
            new ChronologyWhenPeriod($whenPeriod),
            $environmentEvent,
            $experienceFeeling,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }
}
