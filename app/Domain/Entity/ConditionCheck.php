<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class ConditionCheck
{
    private function __construct(
        private ?int $id,
        private int $mood,
        private int $fatigue,
        private int $anxiety,
        private int $sleepiness,
        private int $physicalCondition,
        private ?string $memo,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {
    }

    public static function createNew(
        int $mood,
        int $fatigue,
        int $anxiety,
        int $sleepiness,
        int $physicalCondition,
        ?string $memo,
        DateTimeImmutable $createdAt,
    ): self {
        return new self(null, $mood, $fatigue, $anxiety, $sleepiness, $physicalCondition, $memo, $createdAt, $createdAt);
    }

    public static function reconstitute(
        int $id,
        int $mood,
        int $fatigue,
        int $anxiety,
        int $sleepiness,
        int $physicalCondition,
        ?string $memo,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self($id, $mood, $fatigue, $anxiety, $sleepiness, $physicalCondition, $memo, $createdAt, $updatedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMood(): int
    {
        return $this->mood;
    }

    public function getFatigue(): int
    {
        return $this->fatigue;
    }

    public function getAnxiety(): int
    {
        return $this->anxiety;
    }

    public function getSleepiness(): int
    {
        return $this->sleepiness;
    }

    public function getPhysicalCondition(): int
    {
        return $this->physicalCondition;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
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
            $this->mood,
            $this->fatigue,
            $this->anxiety,
            $this->sleepiness,
            $this->physicalCondition,
            $this->memo,
            $this->createdAt,
            $this->updatedAt,
        );
    }

    public function update(
        int $mood,
        int $fatigue,
        int $anxiety,
        int $sleepiness,
        int $physicalCondition,
        ?string $memo,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            $this->id,
            $mood,
            $fatigue,
            $anxiety,
            $sleepiness,
            $physicalCondition,
            $memo,
            $this->createdAt,
            $updatedAt,
        );
    }
}
