<?php

namespace App\Domain\Entity;

use App\Domain\ValueObject\ChronologyWhenPeriod;
use DateTimeImmutable;

class Chronology
{
    public const SENTIMENT_POSITIVE = 'positive';

    public const SENTIMENT_NEGATIVE = 'negative';

    public const VALID_SENTIMENTS = [self::SENTIMENT_POSITIVE, self::SENTIMENT_NEGATIVE];

    private ?int $id;

    private ChronologyWhenPeriod $whenPeriod;

    private ?string $environmentEvent;

    private ?string $experienceFeeling;

    private ?string $sentimentType;

    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        ChronologyWhenPeriod $whenPeriod,
        ?string $environmentEvent,
        ?string $experienceFeeling,
        ?string $sentimentType,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->whenPeriod = $whenPeriod;
        $this->environmentEvent = $environmentEvent;
        $this->experienceFeeling = $experienceFeeling;
        $this->sentimentType = $sentimentType;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        string $whenPeriod,
        ?string $environmentEvent,
        ?string $experienceFeeling,
        ?string $sentimentType = null
    ): self {
        $now = new DateTimeImmutable('now');

        return new self(
            null,
            new ChronologyWhenPeriod($whenPeriod),
            $environmentEvent,
            $experienceFeeling,
            $sentimentType,
            $now,
            $now
        );
    }

    public static function reconstitute(
        int $id,
        string $whenPeriod,
        ?string $environmentEvent,
        ?string $experienceFeeling,
        ?string $sentimentType,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            new ChronologyWhenPeriod($whenPeriod),
            $environmentEvent,
            $experienceFeeling,
            $sentimentType,
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

    public function getSentimentType(): ?string
    {
        return $this->sentimentType;
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
            $this->sentimentType,
            $this->createdAt,
            $this->updatedAt
        );
    }

    public function update(
        string $whenPeriod,
        ?string $environmentEvent,
        ?string $experienceFeeling,
        ?string $sentimentType = null
    ): self {
        return new self(
            $this->id,
            new ChronologyWhenPeriod($whenPeriod),
            $environmentEvent,
            $experienceFeeling,
            $sentimentType,
            $this->createdAt,
            new DateTimeImmutable('now')
        );
    }
}
