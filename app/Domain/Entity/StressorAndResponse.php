<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class StressorAndResponse
{
    private ?int $id;
    private string $stressor;
    private ?string $cognition;
    private ?string $mood;
    private ?string $bodyReaction;
    private ?string $behavior;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        string $stressor,
        ?string $cognition,
        ?string $mood,
        ?string $bodyReaction,
        ?string $behavior,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->stressor = $stressor;
        $this->cognition = $cognition;
        $this->mood = $mood;
        $this->bodyReaction = $bodyReaction;
        $this->behavior = $behavior;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        string $stressor,
        ?string $cognition,
        ?string $mood,
        ?string $bodyReaction,
        ?string $behavior
    ): self {
        $now = new DateTimeImmutable('now');
        return new self(
            null,
            $stressor,
            $cognition,
            $mood,
            $bodyReaction,
            $behavior,
            $now,
            $now
        );
    }

    public static function reconstitute(
        int $id,
        string $stressor,
        ?string $cognition,
        ?string $mood,
        ?string $bodyReaction,
        ?string $behavior,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            $stressor,
            $cognition,
            $mood,
            $bodyReaction,
            $behavior,
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStressor(): string
    {
        return $this->stressor;
    }

    public function getCognition(): ?string
    {
        return $this->cognition;
    }

    public function getMood(): ?string
    {
        return $this->mood;
    }

    public function getBodyReaction(): ?string
    {
        return $this->bodyReaction;
    }

    public function getBehavior(): ?string
    {
        return $this->behavior;
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
            $this->stressor,
            $this->cognition,
            $this->mood,
            $this->bodyReaction,
            $this->behavior,
            $this->createdAt,
            $this->updatedAt
        );
    }
}
