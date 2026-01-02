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
    /** @var array<string>|null */
    private ?array $stimulatedSchemas;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    /**
     * @param array<string>|null $stimulatedSchemas
     */
    private function __construct(
        ?int $id,
        string $stressor,
        ?string $cognition,
        ?string $mood,
        ?string $bodyReaction,
        ?string $behavior,
        ?array $stimulatedSchemas,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->stressor = $stressor;
        $this->cognition = $cognition;
        $this->mood = $mood;
        $this->bodyReaction = $bodyReaction;
        $this->behavior = $behavior;
        $this->stimulatedSchemas = $stimulatedSchemas;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param array<string>|null $stimulatedSchemas
     */
    public static function createNew(
        string $stressor,
        ?string $cognition,
        ?string $mood,
        ?string $bodyReaction,
        ?string $behavior,
        ?array $stimulatedSchemas = null
    ): self {
        $now = new DateTimeImmutable('now');
        return new self(
            null,
            $stressor,
            $cognition,
            $mood,
            $bodyReaction,
            $behavior,
            $stimulatedSchemas,
            $now,
            $now
        );
    }

    /**
     * @param array<string>|null $stimulatedSchemas
     */
    public static function reconstitute(
        int $id,
        string $stressor,
        ?string $cognition,
        ?string $mood,
        ?string $bodyReaction,
        ?string $behavior,
        ?array $stimulatedSchemas,
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
            $stimulatedSchemas,
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

    /**
     * @return array<string>|null
     */
    public function getStimulatedSchemas(): ?array
    {
        return $this->stimulatedSchemas;
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
            $this->stimulatedSchemas,
            $this->createdAt,
            $this->updatedAt
        );
    }
}
