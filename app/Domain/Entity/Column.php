<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class Column
{
    private ?int $id;
    private string $situation;
    private ?string $mood;
    private ?string $automaticThought;
    private ?string $evidence;
    private ?string $counterEvidence;
    private ?string $adaptiveThought;
    private ?string $currentMood;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        ?int $id,
        string $situation,
        ?string $mood,
        ?string $automaticThought,
        ?string $evidence,
        ?string $counterEvidence,
        ?string $adaptiveThought,
        ?string $currentMood,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->situation = $situation;
        $this->mood = $mood;
        $this->automaticThought = $automaticThought;
        $this->evidence = $evidence;
        $this->counterEvidence = $counterEvidence;
        $this->adaptiveThought = $adaptiveThought;
        $this->currentMood = $currentMood;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function createNew(
        string $situation,
        ?string $mood,
        ?string $automaticThought,
        ?string $evidence,
        ?string $counterEvidence,
        ?string $adaptiveThought,
        ?string $currentMood
    ): self {
        $now = new DateTimeImmutable('now');
        return new self(
            null,
            $situation,
            $mood,
            $automaticThought,
            $evidence,
            $counterEvidence,
            $adaptiveThought,
            $currentMood,
            $now,
            $now
        );
    }

    public static function reconstitute(
        int $id,
        string $situation,
        ?string $mood,
        ?string $automaticThought,
        ?string $evidence,
        ?string $counterEvidence,
        ?string $adaptiveThought,
        ?string $currentMood,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            $situation,
            $mood,
            $automaticThought,
            $evidence,
            $counterEvidence,
            $adaptiveThought,
            $currentMood,
            $createdAt,
            $updatedAt
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSituation(): string
    {
        return $this->situation;
    }

    public function getMood(): ?string
    {
        return $this->mood;
    }

    public function getAutomaticThought(): ?string
    {
        return $this->automaticThought;
    }

    public function getEvidence(): ?string
    {
        return $this->evidence;
    }

    public function getCounterEvidence(): ?string
    {
        return $this->counterEvidence;
    }

    public function getAdaptiveThought(): ?string
    {
        return $this->adaptiveThought;
    }

    public function getCurrentMood(): ?string
    {
        return $this->currentMood;
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
            $this->situation,
            $this->mood,
            $this->automaticThought,
            $this->evidence,
            $this->counterEvidence,
            $this->adaptiveThought,
            $this->currentMood,
            $this->createdAt,
            $this->updatedAt
        );
    }
}
